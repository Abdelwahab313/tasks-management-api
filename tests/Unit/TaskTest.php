<?php

namespace Tests\Unit;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_task_with_valid_data()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'due_date' => now()->addDays(1)->format('Y-m-d')
        ];

        $task = Task::create($taskData);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($taskData['title'], $task->title);
        $this->assertEquals($taskData['description'], $task->description);
        $this->assertEquals($taskData['status'], $task->status);
        $this->assertEquals($taskData['due_date'], $task->due_date->format('Y-m-d'));
    }

    public function test_task_status_must_be_valid()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Task::create([
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'invalid_status',
            'due_date' => now()->addDays(1)->format('Y-m-d')
        ]);
    }
}
