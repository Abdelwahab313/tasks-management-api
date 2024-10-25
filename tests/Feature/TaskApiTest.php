<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_task()
    {
        $taskData = [
            'title' => 'New Task',
            'description' => 'Task Description',
            'status' => 'pending',
            'due_date' => now()->addDays(1)->format('Y-m-d')
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJson(['data' => [
                'title' => 'New Task',
                'status' => 'pending'
            ]]);
    }

    public function test_cannot_create_task_with_invalid_data()
    {
        $response = $this->postJson('/api/tasks', [
            'title' => '',
            'status' => 'invalid_status',
            'due_date' => 'invalid_date'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status', 'due_date']);
    }

    public function test_can_get_tasks_with_filters()
    {
        // Create test tasks
        Task::create([
            'title' => 'Task 1',
            'description' => 'Description 1',
            'status' => 'pending',
            'due_date' => now()->addDays(1)
        ]);

        Task::create([
            'title' => 'Task 2',
            'description' => 'Description 2',
            'status' => 'completed',
            'due_date' => now()->addDays(2)
        ]);

        // Test filtering by status
        $response = $this->getJson('/api/tasks?status=pending');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'pending');

        // Test filtering by due_date
        $response = $this->getJson('/api/tasks?due_date=' . now()->addDays(1)->format('Y-m-d'));
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
