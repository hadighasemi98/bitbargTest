<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_index_shoukld_aklsdas(): void
    {
        Task::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/tasks');

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

    public function test_store(): void
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'due_date' => now()->addDays(5)->toDateString(),
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Task created successfully',
                'task' => [
                    'title' => 'Test Task',
                    'description' => 'Test Description',
                    'due_date' => now()->addDays(5)->toDateString(),
                    'user_id' => $this->user->id,
                ],
            ]);

        $this->assertDatabaseHas('tasks', $taskData);
    }

    public function test_show(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'due_date' => $task->due_date,
                'user_id' => $this->user->id,
            ]);
    }

    public function test_update(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'title' => 'Updated Task',
            'description' => 'Updated Description',
            'due_date' => now()->addDays(10)->toDateString(),
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task updated successfully',
                'task' => [
                    'title' => 'Updated Task',
                    'description' => 'Updated Description',
                    'due_date' => now()->addDays(10)->toDateString(),
                ],
            ]);

        $this->assertDatabaseHas('tasks', $updateData);
    }

    public function test_destroy(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Task deleted successfully']);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_mark_as_completed()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id, 'status' => 'pending']);
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);

        $this->actingAs($user, 'api')
            ->patchJson(route('tasks.complete', ['id' => $task->id]))
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task marked as completed',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);
    }

    public function test_mark_as_pending()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id, 'status' => 'completed']);
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);

        $this->actingAs($user, 'api')
            ->patchJson(route('tasks.pending', ['id' => $task->id]))
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task marked as pending',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'pending',
        ]);
    }
}
