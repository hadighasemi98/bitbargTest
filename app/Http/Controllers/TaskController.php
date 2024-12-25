<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\CreateTaskRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService)
    {
        $this->middleware('auth:api');
        $this->middleware('permission:manage tasks', ['only' => ['store', 'update', 'destroy']]);
        $this->middleware('permission:view tasks', ['only' => ['index', 'show']]);
    }

    public function index(): JsonResponse {
        return $this->taskService->index();
    }

    public function store(CreateTaskRequest $request): JsonResponse
    {
        return $this->taskService->store($request);
    }

    public function show($id): JsonResponse {
        return $this->taskService->show($id);
    }

    public function update(Request $request, $id): JsonResponse {
        return $this->taskService->update($request, $id);
    }

    public function destroy($id): JsonResponse {
        return $this->taskService->destroy($id);
    }
}
