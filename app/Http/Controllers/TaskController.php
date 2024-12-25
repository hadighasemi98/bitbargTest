<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Services\TaskManagement\TaskService;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService) {}

    public function index(): JsonResponse
    {
        return $this->taskService->index();
    }

    public function store(CreateTaskRequest $request): JsonResponse
    {
        return $this->taskService->store($request);
    }

    public function show($id): JsonResponse
    {
        return $this->taskService->show($id);
    }

    public function update(UpdateTaskRequest $request, $id): JsonResponse
    {
        return $this->taskService->update($request, $id);
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->taskService->destroy($id);
    }

    public function markAsCompleted(int $id): JsonResponse
    {
        return $this->taskService->markAsCompleted($id);
    }

    public function markAsPending(int $id): JsonResponse
    {
        return $this->taskService->markAsPending($id);
    }
}
