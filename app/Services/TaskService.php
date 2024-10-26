<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskService
{
    public function __construct(
        private readonly TaskRepository $taskRepository
    ) {}

    public function getTasks(array $filters): LengthAwarePaginator
    {
        return $this->taskRepository->getTasksWithFilters($filters);
    }

    public function getTask(int $id): Task
    {
        return $this->taskRepository->findOrFail($id);
    }

    public function createTask(array $data): Task
    {
        return $this->taskRepository->create($data);
    }

    public function updateTask(int $id, array $data): Task
    {
        return $this->taskRepository->update($id, $data);
    }

    public function deleteTask(int $id): bool
    {
        return $this->taskRepository->delete($id);
    }
}
