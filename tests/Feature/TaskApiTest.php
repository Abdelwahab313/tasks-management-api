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
        $taskData = Task::factory()->pending()->make()->toArray();

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJson(['data' => [
                'title' => $taskData['title'],
                'status' => $taskData['status'],
                'due_date' => $taskData['due_date'],
            ]]);

        // Assert data was actually saved in a database
        $this->assertDatabaseHas('tasks', [
            'title' => $taskData['title'],
            'status' => $taskData['status'],
            'due_date' => $taskData['due_date'],
        ]);
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

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_can_get_all_tasks_ordered_by_due_date()
    {

        $pastDueTask = Task::factory()->create(['due_date' => now()->subDays(1)]);
        $todayTask = Task::factory()->create(['due_date' => now()]);
        $tomorrowDueTask = Task::factory()->create(['due_date' => now()->addDays(1)]);

        $response = $this->getJson('/api/tasks');
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data.data')
            ->assertJsonPath('data.data.0.id', $pastDueTask->id)
            ->assertJsonPath('data.data.1.id', $todayTask->id)
            ->assertJsonPath('data.data.2.id', $tomorrowDueTask->id);

//
//        // Test filtering by due_date
//        $response = $this->getJson('/api/tasks?due_date=' . now()->addDays(1)->format('Y-m-d'));
//        $response->assertStatus(200)
//            ->assertJsonCount(1, 'data.data')
//            ->assertJsonPath('data.data.0.id', $pendingTask->id);
//
//        // Verify database state after test
//        $this->assertDatabaseCount('tasks', 2);
    }

    public function test_can_filter_tasks_by_status()
    {
        // Create a task
        Task::factory()->pending()->create();
        Task::factory()->completed()->create();

        $response = $this->getJson('/api/tasks?status=completed');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.status', Task::STATUS_COMPLETED);
    }

    public function test_can_filter_tasks_by_status_with_invalid_status()
    {
        // Create a task
        Task::factory()->pending()->create();
        Task::factory()->completed()->create();

        $response = $this->getJson('/api/tasks?status=invalid_status');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_can_filter_by_title()
    {
        $task1 = Task::factory()->pending()->create(['title' => 'Task 1', 'due_date' => now()]);
        $task2 = Task::factory()->pending()->create(['title' => 'Task 2', 'due_date' => now()->addDays(1)]);
        $task3 = Task::factory()->pending()->create(['title' => 'NOT RECORDED']);

        $response = $this->getJson('/api/tasks?title=Task');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data.data')
            ->assertJsonPath('data.data.0.title', 'Task 1');
    }

    public function test_can_filter_by_due_date()
        {
            $task1 = Task::factory()->pending()->create(['due_date' => now()]);
            $task2 = Task::factory()->pending()->create(['due_date' => now()->addDays(1)]);
            $task3 = Task::factory()->pending()->create(['due_date' => now()->addDays(2)]);

            $response = $this->getJson('/api/tasks?due_date=' . now()->format('Y-m-d'));
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data.data')
                ->assertJsonPath('data.data.0.id', $task1->id);
    }

    public function test_can_get_task_by_id()
    {
        $task = Task::factory()->pending()->create();

        $response = $this->getJson('/api/tasks/' . $task->id);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'title' => $task->title,
                'status' => $task->status,
                'description' => $task->description,
            ]]);
    }

    public function test_cannot_get_task_by_invalid_id()
    {
        $response = $this->getJson('/api/tasks/invalid_id');

        $response->assertStatus(404);
    }


    public function test_can_get_empty_tasks_list()
    {
        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data.data');
    }


}
