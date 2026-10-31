<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    // --- US8 / CA1 -------------------------------------------------------
    public function test_guest_is_redirected_to_login_when_accessing_projects(): void
    {
        $this->get(route('projects.index'))
            ->assertRedirect(route('login'));
    }

    // --- US8 / CA2 -------------------------------------------------------
    public function test_user_can_create_a_project(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('projects.store'), [
            'name' => 'Mon premier projet',
            'description' => 'Une description',
        ]);

        $project = Project::firstOrFail();

        $response->assertRedirect(route('projects.show', $project));
        $this->assertDatabaseHas('projects', [
            'name' => 'Mon premier projet',
            'description' => 'Une description',
            'user_id' => $user->id,
        ]);
    }

    // --- Bonus : validation, filtrage et sécurité -------------------------
    public function test_project_name_is_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('projects.store'), ['name' => ''])
            ->assertSessionHasErrors('name');

        $this->assertDatabaseCount('projects', 0);
    }

    public function test_index_only_lists_the_users_own_projects(): void
    {
        $user = User::factory()->create();
        $mine = Project::factory()->for($user)->create(['name' => 'Projet visible']);
        $theirs = Project::factory()->create(['name' => 'Projet secret']);

        $this->actingAs($user)
            ->get(route('projects.index'))
            ->assertOk()
            ->assertSee($mine->name)
            ->assertDontSee($theirs->name);
    }

    public function test_user_can_update_and_delete_own_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $this->actingAs($user)
            ->put(route('projects.update', $project), ['name' => 'Nouveau nom'])
            ->assertRedirect(route('projects.show', $project));
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Nouveau nom']);

        $this->actingAs($user)
            ->delete(route('projects.destroy', $project))
            ->assertRedirect(route('projects.index'));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_user_cannot_access_another_users_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(); // appartient à quelqu'un d'autre

        $this->actingAs($user)->get(route('projects.show', $project))->assertForbidden();
        $this->actingAs($user)->get(route('projects.edit', $project))->assertForbidden();
        $this->actingAs($user)->put(route('projects.update', $project), ['name' => 'Piraté'])->assertForbidden();
        $this->actingAs($user)->delete(route('projects.destroy', $project))->assertForbidden();

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => $project->name]);
    }
}
