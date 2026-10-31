<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private function userWithProject(): array
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        return [$user, $project];
    }

    // --- US8 / CA3 -------------------------------------------------------
    public function test_task_title_is_required(): void
    {
        [$user, $project] = $this->userWithProject();

        $this->actingAs($user)
            ->post(route('projects.tasks.store', $project), [
                'title' => '',
                'status' => 'pending',
            ])
            ->assertSessionHasErrors('title');

        $this->assertDatabaseCount('tasks', 0);
    }

    // --- Bonus -----------------------------------------------------------
    public function test_task_status_must_be_one_of_the_three_values(): void
    {
        [$user, $project] = $this->userWithProject();

        $this->actingAs($user)
            ->post(route('projects.tasks.store', $project), [
                'title' => 'Tâche',
                'status' => 'invalide',
            ])
            ->assertSessionHasErrors('status');
    }

    public function test_task_deadline_must_be_a_valid_date(): void
    {
        [$user, $project] = $this->userWithProject();

        $this->actingAs($user)
            ->post(route('projects.tasks.store', $project), [
                'title' => 'Tâche',
                'status' => 'pending',
                'deadline' => 'pas-une-date',
            ])
            ->assertSessionHasErrors('deadline');
    }

    public function test_user_can_create_a_task(): void
    {
        [$user, $project] = $this->userWithProject();

        $this->actingAs($user)
            ->post(route('projects.tasks.store', $project), [
                'title' => 'Écrire les tests',
                'status' => 'in_progress',
                'deadline' => '2030-01-15',
            ])
            ->assertRedirect(route('projects.show', $project));

        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'title' => 'Écrire les tests',
            'status' => 'in_progress',
        ]);
    }

    public function test_user_can_upload_an_attachment(): void
    {
        Storage::fake('public');
        [$user, $project] = $this->userWithProject();

        $this->actingAs($user)
            ->post(route('projects.tasks.store', $project), [
                'title' => 'Tâche avec fichier',
                'status' => 'pending',
                'attachment' => UploadedFile::fake()->create('cahier-des-charges.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasNoErrors();

        $task = Task::firstOrFail();

        $this->assertStringStartsWith('attachments/', $task->attachment_path);
        Storage::disk('public')->assertExists($task->attachment_path);
    }

    public function test_user_can_change_task_status_quickly(): void
    {
        [$user, $project] = $this->userWithProject();
        $task = Task::factory()->for($project)->create(['status' => 'pending']);

        $this->actingAs($user)
            ->patch(route('projects.tasks.status', [$project, $task]), ['status' => 'done'])
            ->assertRedirect(route('projects.show', $project));

        $this->assertSame('done', $task->fresh()->status);
    }

    public function test_user_cannot_touch_tasks_of_another_users_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();            // projet d'un autre
        $task = Task::factory()->for($project)->create();

        $this->actingAs($user)
            ->get(route('projects.tasks.create', $project))
            ->assertForbidden();

        $this->actingAs($user)
            ->post(route('projects.tasks.store', $project), ['title' => 'Intrus', 'status' => 'pending'])
            ->assertForbidden();

        $this->actingAs($user)
            ->delete(route('projects.tasks.destroy', [$project, $task]))
            ->assertForbidden();

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_task_must_belong_to_the_project_in_the_url(): void
    {
        [$user, $project] = $this->userWithProject();
        $otherTask = Task::factory()->create(); // tâche d'un autre projet

        $this->actingAs($user)
            ->delete(route('projects.tasks.destroy', [$project, $otherTask]))
            ->assertNotFound();

        $this->assertDatabaseHas('tasks', ['id' => $otherTask->id]);
    }
}
