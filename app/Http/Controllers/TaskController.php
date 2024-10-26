<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\ListTasksRequest;
use App\Http\Requests\ShowTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function index(ListTasksRequest $request): JsonResponse
    {
        $query = Task::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }

        if ($request->has('title')) {
            $query->where('title', 'like', "%{$request->title}%");
        }

        $tasks = $query->orderBy('due_date')->paginate($request->per_page ?? 10);

        return response()->json([
            'data' => TaskResource::collection($tasks),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total()
            ]
        ]);
    }

    public function store(CreateTaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());

        return response()->json(['data' => new TaskResource($task)], Response::HTTP_CREATED);
    }

    public function show(ShowTaskRequest $request): JsonResponse
    {
        try {
            $task = Task::findOrFail($request->id);

            return response()->json([
                'data' => new TaskResource($task)
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Task not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

}
