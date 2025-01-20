<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that it returns tasks for authenticated user
     */
    public function test_it_returns_tasks_for_authenticated_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Task::factory()->count(3)->create([
            'user_id' => $user1->id,
        ]);

        Task::factory()->count(2)->create([
            'user_id' => $user2->id,
        ]);

        $token = JWTAuth::fromUser($user1);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/tasks');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'tasks.data');
        $response->assertJsonStructure([
            'status',
            'tasks' => [
                'current_page',
                'data' => [
                    '*' => ['id', 'title', 'description', 'user_id'],
                ],
            ],
        ]);

        $this->assertEquals('success', $response->json('status'));
    }

    /**
     * Test that it requires JWT token to access index
     */
    public function test_it_requires_jwt_token_to_access_index()
    {
        $response = $this->getJson('/api/tasks');

        $response->assertStatus(401);
    }

    /**
     * Test that it creates a new task
     */
    public function test_store_creates_a_new_task()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $payload = [
            'title'       => 'Mi tarea de prueba',
            'description' => 'Descripción de la tarea'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/tasks', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('tasks', [
            'title'   => $payload['title'],
            'user_id' => $user->id,
        ]);

        $response->assertJsonStructure([
            'status',
            'task' => [
                'id',
                'title',
                'description',
                'user_id',
                'created_at',
                'updated_at'
            ],
        ]);

        $this->assertEquals($payload['title'], $response->json('task.title'));
        $this->assertEquals($user->id, $response->json('task.user_id'));
    }

    /**
     * Test that it requires JWT token to store a task
     */
    public function test_store_requires_jwt_token()
    {
        $payload = [
            'title'       => 'Tarea sin token',
            'description' => 'No debe pasar'
        ];

        $response = $this->postJson('/api/tasks', $payload);
        $response->assertStatus(401);

        $this->assertDatabaseMissing('tasks', [
            'title' => $payload['title'],
        ]);
    }

    /**
     * Test that it requires a title to store a task
     */
    public function test_store_validation_error()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $payload = [];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/tasks', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }

    /**
     * Test that it shows a task for the owner
     */
    public function test_show_returns_task_for_owner()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title'   => 'Tarea del Owner'
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
            'task'   => [
                'id'    => $task->id,
                'title' => $task->title,
            ],
        ]);
    }

    /**
     * Test that it returns 404 for non-existing task
     */
    public function test_show_returns_403_for_non_owner()
    {
        $owner = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $owner->id,
            'title'   => 'Tarea Ajena'
        ]);

        $otherUser = User::factory()->create();
        $token = JWTAuth::fromUser($otherUser);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);

        $this->assertEquals('Unauthorized', $response->json('message'));
    }

    /**
     * Test update modifies task for owner
     */
    public function test_update_modifies_task_for_owner()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title'   => 'Old Title'
        ]);

        $token = JWTAuth::fromUser($user);

        $payload = [
            'title'       => 'New Title',
            'description' => 'New Description',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/tasks/{$task->id}", $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', [
            'id'          => $task->id,
            'title'       => $payload['title'],
            'description' => $payload['description'],
        ]);

        $response->assertJson([
            'status' => 'success',
            'task'   => [
                'id'          => $task->id,
                'title'       => $payload['title'],
                'description' => $payload['description'],
            ],
        ]);
    }

    /**
     * Test that it returns 403 for non-owner
     */
    public function test_update_returns_403_for_non_owner()
    {
        $owner = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $owner->id,
            'title'   => 'Titulo Original'
        ]);

        $otherUser = User::factory()->create();
        $token = JWTAuth::fromUser($otherUser);

        $payload = [
            'title' => 'Nuevo Titulo de un intruso'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/tasks/{$task->id}", $payload);

        $response->assertStatus(403);

        $this->assertDatabaseHas('tasks', [
            'id'    => $task->id,
            'title' => $task->title,
        ]);
    }

    /**
     * Test that it requires JWT token to update a task
     */
    public function test_update_validation_error()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $token = JWTAuth::fromUser($user);

        $payload = [
            'title' => '',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/tasks/{$task->id}", $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }

    /**
     * Test that it deletes a task for the owner
     */
    public function test_destroy_deletes_task_for_owner()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title'   => 'Task to be deleted'
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);

        $response->assertJson([
            'status'  => 'success',
            'message' => 'Task deleted successfully',
        ]);
    }

    /**
     * Test that it returns 403 for non-owner
     */
    public function test_destroy_returns_403_for_non_owner()
    {
        $owner = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $owner->id,
            'title'   => 'Task Ajena'
        ]);

        $otherUser = User::factory()->create();
        $token = JWTAuth::fromUser($otherUser);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'user_id' => $owner->id,
        ]);
    }
}
