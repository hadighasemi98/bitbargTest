<?php

namespace App\Services\TaskManagement;

use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Jobs\SendLogJob;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

/**
 * @test Tests\Feature\TaskServiceTest
 */
class TaskService
{
    public function index(): JsonResponse
    {
        $tasks = cache()->remember('tasks_user_'.auth()->user()->id, now()->addMinutes(5), function () {
            return Task::where('user_id', auth()->user()->id)->simplePaginate(config('pagination.per_page'));
        });

        return response()->json([
            'success' => true,
            'data' => $tasks->items(),
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

        cache()->forget('tasks_user_'.auth()->user()->id);

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
        cache()->forget('tasks_user_'.auth()->user()->id);

        $this->senLog($task);

        return response()->json(['success' => true, 'message' => 'Task updated successfully']);
    }

    public function markAsCompleted(int $id): JsonResponse
    {
        $task = Task::where('user_id', auth()->user()->id)->findOrFail($id);

        if ($task->status == Task::STATUS['completed']) {
            return response()->json(['success' => false, 'message' => 'Task already marked as completed'], 406);
        }

        $task->update(['status' => Task::STATUS['completed']]);
        cache()->forget('tasks_user_'.auth()->user()->id);

        return response()->json(['success' => true, 'message' => 'Task marked as completed']);
    }

    public function markAsPending(int $id): JsonResponse
    {
        $task = Task::where('id', $id)->where('user_id', auth()->user()->id)->firstOrFail();

        if ($task->status == Task::STATUS['pending']) {
            return response()->json(['success' => false, 'message' => 'Task already marked as pending'], 406);
        }

        $task->update(['status' => Task::STATUS['pending']]);
        cache()->forget('tasks_user_'.auth()->user()->id);

        return response()->json(['success' => true, 'message' => 'Task marked as pending']);
    }

    public function destroy(int $id): JsonResponse
    {
        $task = Task::where('user_id', auth()->user()->id)->findOrFail($id);

        $this->senLog($task);

        $task->delete();
        cache()->forget('tasks_user_'.auth()->user()->id);

        return response()->json(['success' => true, 'message' => 'Task deleted successfully']);
    }

    private function senLog(Task $task): void
    {
        SendLogJob::dispatch(arguments: $task);
    }
}
