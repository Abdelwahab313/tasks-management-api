<?php

namespace Tests\Feature;

use App\Models\Task;
use Carbon\Carbon;
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
                'due_date' => Carbon::parse($taskData['due_date'])->format('Y-m-d'),
            ]]);

        // Assert data was actually saved in a database
        $this->assertDatabaseHas('tasks', [
            'title' => $taskData['title'],
            'status' => $taskData['status'],
            'due_date' =>  Carbon::parse($taskData['due_date'])->format('Y-m-d'),
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
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.id', $pastDueTask->id)
            ->assertJsonPath('data.1.id', $todayTask->id)
            ->assertJsonPath('data.2.id', $tomorrowDueTask->id);
    }

    public function test_can_filter_tasks_by_status()
    {
        // Create a task
        Task::factory()->pending()->create();
        Task::factory()->completed()->create();

        $response = $this->getJson('/api/tasks?status=completed');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', Task::STATUS_COMPLETED);
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
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.title', 'Task 1');
    }

    public function test_can_filter_by_due_date()
        {
            $task1 = Task::factory()->pending()->create(['due_date' => now()]);
            $task2 = Task::factory()->pending()->create(['due_date' => now()->addDays(1)]);
            $task3 = Task::factory()->pending()->create(['due_date' => now()->addDays(2)]);

            $response = $this->getJson('/api/tasks?due_date=' . now()->format('Y-m-d'));
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.id', $task1->id);
    }



    public function test_should_get_tasks_with_pagination()
    {
        $tasks = Task::factory()->count(10)->create();

        $response = $this->getJson('/api/tasks?page=2&per_page=5');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('meta.total', 10);
    }

    public function test_should_get_tasks_with_pagination_and_filter()
    {
        $tasks = Task::factory()->completed()->count(10)->create();
        $filteredOutTasks = Task::factory()->pending()->count(2)->create();

        $response = $this->getJson('/api/tasks?page=2&per_page=5&status=' . Task::STATUS_COMPLETED);

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('meta.total', 10);
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
            ->assertJsonCount(0, 'data');
    }



}
