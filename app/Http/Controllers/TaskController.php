<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\DeleteTaskRequest;
use App\Http\Requests\ListTasksRequest;
use App\Http\Requests\ShowTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService
    ) {}

    public function index(ListTasksRequest $request): JsonResponse
    {
        $tasks = $this->taskService->getTasks($request->validated());

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
        $task = $this->taskService->createTask($request->validated());

        return response()->json([
            'data' => new TaskResource($task)
        ], Response::HTTP_CREATED);
    }

    public function show(ShowTaskRequest $request): JsonResponse
    {

        try {
            $task = $this->taskService->getTask($request->validated('id'));

            return response()->json([
                'data' => new TaskResource($task)
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Task not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(UpdateTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->updateTask(
            $request->validated('id'),
            $request->validated()
        );

        return response()->json([
            'data' => new TaskResource($task)
        ]);
    }

    public function destroy(DeleteTaskRequest $request): JsonResponse
    {
        $this->taskService->deleteTask($request->validated('id'));

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }

}
