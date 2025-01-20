<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that it registers a user successfully
     */
    public function test_register_success()
    {
        $payload = [
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => 'secret123'
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
            'authorization' => [
                'token',
                'type'
            ]
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $payload['email'],
            'name'  => $payload['name'],
        ]);

        $this->assertEquals('success', $response->json('status'));
        $this->assertEquals('User created successfully', $response->json('message'));
    }

    /**
     * Test that it returns validation error when registering a user
     */
    public function test_register_validation_error()
    {
        $payload = [
            'name'     => 'User Fail',
            'password' => 'secret123',
        ];

        $response = $this->postJson('/api/register', $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);

        $this->assertDatabaseMissing('users', [
            'name'  => $payload['name'],
        ]);
    }

    /**
     * Test that it logs in a user successfully
     */
    public function test_login_success()
    {
        $payload = [
            'email'    => 'login@example.com',
            'password' => 'secret123',
        ];

        User::factory()->create([
            'email'    => $payload['email'],
            'password' => bcrypt($payload['password']),
        ]);

        $response = $this->postJson('/api/login', $payload);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token'
        ]);

        $this->assertNotNull($response->json('token'));
    }

    /**
     * Test that it returns validation error when logging in a user
     */
    public function test_login_invalid_credentials()
    {
        $payload = [
            'email'    => 'fail@example.com',
            'password' => 'wrongpassword',
        ];

        User::factory()->create([
            'email'    => $payload['email'],
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', $payload);
        $response->assertStatus(401);
        $response->assertJson([
            'error' => 'Invalid credentials'
        ]);
    }

    /**
     * Test that it logs out a user successfully
     */
    public function test_logout_success()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out',
                'status'  => 'success'
            ]);
    }
}
