<?php

namespace App\Services\TaskManagement;

use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @test Tests\Feature\TaskServiceTest
 */
class TaskService
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Task::where('user_id', auth()->user()->id)->simplePaginate(perPage: config('pagination.per_page')),
        ]);
    }

    public function store(CreateTaskRequest $request): JsonResponse
    {
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'user_id' => auth()->user()->id,
        ]);

        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Task::where('user_id', auth()->user()->id)->findOrFail($id),
        ]);
    }

    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {

        $task = Task::where('user_id', auth()->user()->id)->findOrFail($id);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => $request->status ?? $task->status,
        ]);

        activity()->causedBy(Auth::user()->id)->performedOn($task)->log('Updated a task');

        return response()->json(['success' => true, 'message' => 'Task updated successfully']);
    }

    public function markAsCompleted(int $id): JsonResponse
    {
        $task = Task::where('user_id', auth()->user()->id)->findOrFail($id);

        if ($task->status == Task::STATUS['completed']) {
            return response()->json(['success' => false, 'message' => 'Task already marked as completed'], 406);
        }

        $task->update(['status' => Task::STATUS['completed']]);

        return response()->json(['success' => true, 'message' => 'Task marked as completed']);
    }

    public function markAsPending(int $id): JsonResponse
    {
        $task = Task::where('id', $id)->where('user_id', auth()->user()->id)->firstOrFail();

        if ($task->status == Task::STATUS['pending']) {
            return response()->json(['success' => false, 'message' => 'Task already marked as pending'], 406);
        }

        $task->update(['status' => Task::STATUS['pending']]);

        return response()->json(['success' => true, 'message' => 'Task marked as pending']);
    }

    public function destroy(int $id): JsonResponse
    {
        $task = Task::where('user_id', auth()->user()->id)->findOrFail($id);

        activity()->causedBy(Auth::user()->id)->performedOn($task)->log('Deleted a task');

        $task->delete();

        return response()->json(['success' => true, 'message' => 'Task deleted successfully']);
    }
}
