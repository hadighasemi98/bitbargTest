<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan(
            'passport:client',
            ['--name' => 'user', '--personal' => null]
        );

    }

    public function test_must_return_all_task_of_user_with_paginate_and_proper_permission(): void
    {
        $user = User::factory()->create();
        Task::factory()->count(3)->create(['user_id' => $user->id]);
        (new Permission)->create(['name' => 'view-tasks']);

        $user->givePermissionTo('view-tasks');

        $response = $this->getJson('/api/tasks', [
            'Authorization' => 'Bearer '.$user->createToken('user')->accessToken,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'due_date',
                        'user_id',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_must_store_task_for_user_with_proper_permission(): void
    {
        $user = User::factory()->create();

        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'due_date' => now()->addDays(5)->toDateString(),
        ];

        (new Permission)->create(['name' => 'manage-tasks']);

        $user->givePermissionTo('manage-tasks');

        $response = $this->postJson('/api/tasks', $taskData, [
            'Authorization' => 'Bearer '.$user->createToken('user')->accessToken,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Task created successfully',
                'task' => [
                    'title' => 'Test Task',
                    'description' => 'Test Description',
                    'due_date' => now()->addDays(5)->toDateString(),
                    'user_id' => $user->id,
                ],
            ]);

        $this->assertDatabaseHas('tasks', $taskData);
        $this->assertDatabaseCount('tasks', 1);
    }

    public function test_must_not_store_task_for_user_with_wrong_permission(): void
    {
        $user = User::factory()->create();

        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'due_date' => now()->addDays(5)->toDateString(),
        ];

        (new Permission)->create(['name' => 'view-tasks']);

        $user->givePermissionTo('view-tasks');

        $response = $this->postJson('/api/tasks', $taskData, [
            'Authorization' => 'Bearer '.$user->createToken('user')->accessToken,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_must_show_specific_task_id_with_proper_permission(): void
    {
        $task = Task::factory()->create();

        (new Permission)->create(['name' => 'view-tasks']);

        $task->user->givePermissionTo('view-tasks');

        $response = $this->getJson("/api/tasks/{$task->id}", [
            'Authorization' => 'Bearer '.$task->user->createToken('user')->accessToken,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'due_date' => $task->due_date,
                    'user_id' => $task->user->id,
                ],
            ]);
    }

    public function test_must_update_a_task_with_proper_permission(): void
    {
        $task = Task::factory()->create();

        $updateData = [
            'title' => 'Updated Task',
            'description' => 'Updated Description',
            'due_date' => now()->addDays(10)->toDateString(),
        ];

        (new Permission)->create(['name' => 'manage-tasks']);

        $task->user->givePermissionTo('manage-tasks');

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData, [
            'Authorization' => 'Bearer '.$task->user->createToken('user')->accessToken,

        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task updated successfully',
            ]);

        $this->assertDatabaseCount('tasks', 1);
    }

    public function test_must_delete_a_test_with_delete_permission(): void
    {
        $task = Task::factory()->create();

        (new Permission)->create(['name' => 'delete-tasks']);

        $task->user->givePermissionTo('delete-tasks');

        $response = $this->deleteJson("/api/tasks/{$task->id}", [], [
            'Authorization' => 'Bearer '.$task->user->createToken('user')->accessToken,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task deleted successfully',
            ]);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_must_mark_as_completed_a_pending_task(): void
    {
        $task = Task::factory()->create(['status' => Task::STATUS['pending']]);

        (new Permission)->create(['name' => 'manage-tasks']);
        $task->user->givePermissionTo('manage-tasks');

        $response = $this->patchJson("/api/tasks/{$task->id}/complete", [], [
            'Authorization' => 'Bearer '.$task->user->createToken('user')->accessToken,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task marked as completed',
            ]);
    }

    public function test_must_mark_as_pending_back_a_completed_task(): void
    {
        $task = Task::factory()->create(['status' => Task::STATUS['completed']]);

        (new Permission)->create(['name' => 'manage-tasks']);
        $task->user->givePermissionTo('manage-tasks');

        $response = $this->patchJson("/api/tasks/{$task->id}/pending", [], [
            'Authorization' => 'Bearer '.$task->user->createToken('user')->accessToken,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task marked as pending',
            ]);
    }
}
