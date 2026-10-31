<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_label_accessor_returns_french_labels(): void
    {
        $this->assertSame('En attente', (new Task(['status' => 'pending']))->status_label);
        $this->assertSame('En cours', (new Task(['status' => 'in_progress']))->status_label);
        $this->assertSame('Terminée', (new Task(['status' => 'done']))->status_label);
    }

    public function test_overdue_scope_only_returns_late_unfinished_tasks(): void
    {
        $project = Project::factory()->create();

        $late = Task::factory()->for($project)->create(['deadline' => now()->subDays(2), 'status' => 'pending']);
        Task::factory()->for($project)->create(['deadline' => now()->addDays(2), 'status' => 'pending']); // pas encore due
        Task::factory()->for($project)->create(['deadline' => now()->subDay(), 'status' => 'done']);      // terminée
        Task::factory()->for($project)->create(['deadline' => null, 'status' => 'pending']);               // sans échéance

        $this->assertSame([$late->id], Task::overdue()->pluck('id')->all());
        $this->assertTrue($late->isOverdue());
    }

    public function test_attachment_url_is_null_without_attachment(): void
    {
        $this->assertNull((new Task())->attachment_url);
    }
}
