<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_task(): void
    {
        $response = $this->post('/tasks', [
            'task_name' => 'Prepare sprint review',
            'description' => 'Review blockers and next milestones.',
            'status' => 'Pending',
            'due_date' => '2026-09-30',
        ]);

        $response->assertRedirect('/tasks');
        $this->assertDatabaseHas('tasks', [
            'task_name' => 'Prepare sprint review',
            'status' => 'Pending',
        ]);
    }

    public function test_user_can_update_status_and_delete_a_task(): void
    {
        $task = Task::factory()->create([
            'task_name' => 'Draft weekly summary',
            'status' => 'Pending',
        ]);

        $this->patch("/tasks/{$task->id}/status", [
            'status' => 'Completed',
        ])
            ->assertRedirect('/tasks');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'Completed',
        ]);

        $this->delete("/tasks/{$task->id}")
            ->assertRedirect('/tasks');

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_user_can_create_a_task_in_progress(): void
    {
        $this->post('/tasks', [
            'task_name' => 'Build dashboard filters',
            'description' => 'Add status and overdue views.',
            'status' => 'In Progress',
            'due_date' => '2026-09-30',
        ])->assertRedirect('/tasks');

        $this->assertDatabaseHas('tasks', [
            'task_name' => 'Build dashboard filters',
            'status' => 'In Progress',
        ]);
    }
}
