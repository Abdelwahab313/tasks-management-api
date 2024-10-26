<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskRepository
{
    public function getTasksWithFilters(array $filters): LengthAwarePaginator
    {
        $query = Task::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['due_date'])) {
            $query->whereDate('due_date', $filters['due_date']);
        }

        if (isset($filters['title'])) {
            $query->where('title', 'like', "%{$filters['title']}%");
        }

        // Get the page number from filters, default to 1 if not set
        $page = $filters['page'] ?? 1;

        // Get items per page from filters, default to 15 if not set
        $perPage = $filters['per_page'] ?? 10;

        return $query->orderBy('due_date')->paginate($perPage, ['*'], 'page', $page);
    }

    public function findOrFail(int $id): Task
    {
        return Task::findOrFail($id);
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(int $id, array $data): Task
    {
        $task = $this->findOrFail($id);
        $task->update($data);
        return $task->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->findOrFail($id)->delete();
    }
}
