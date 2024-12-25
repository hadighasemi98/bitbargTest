<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\CreateTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    public function index(): JsonResponse
    {
        return response()->json(Task::where('user_id', Auth::id())->simplePaginate(perPage: config('pagination.per_page')));
    }

    public function store(CreateTaskRequest $request): JsonResponse
    {
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }

    public function show($id): JsonResponse
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        return response()->json($task);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $task->title = $request->title;
        $task->description = $request->description;
        $task->due_date = $request->due_date;
        $task->save();

        return response()->json(['message' => 'Task updated successfully', 'task' => $task]);
    }

    public function destroy($id): JsonResponse
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
