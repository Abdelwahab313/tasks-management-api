<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\ListTasksRequest;
use App\Http\Requests\ShowTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

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

        $tasks = $query->orderBy('due_date')->paginate();

        return response()->json(['data' => $tasks]);
    }

    public function store(CreateTaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());

        return response()->json(['data' => $task], 201);
    }

    public function show(ShowTaskRequest $request): JsonResponse
    {
        $task = Task::findOrFail($request->id);

        return response()->json(['data' => $task]);
    }

}
