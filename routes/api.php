<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:api'])->group(function () {
    Route::middleware(['can:view-tasks'])->group(function () {
        Route::get('/tasks', [TaskController::class, 'index']);
        Route::get('/tasks/{id}', [TaskController::class, 'show']);
    });

    Route::middleware(['can:manage-tasks'])->group(function () {
        Route::post('/tasks', [TaskController::class, 'store']);
        Route::put('/tasks/{id}', [TaskController::class, 'update']);

        Route::patch('tasks/{id}/complete', [TaskController::class, 'markAsCompleted']);
        Route::patch('tasks/{id}/pending', [TaskController::class, 'markAsPending']);

    });

    Route::middleware(['can:delete-tasks'])->group(function () {

        Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
    });

});
