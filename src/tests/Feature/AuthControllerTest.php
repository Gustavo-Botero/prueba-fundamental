<?php

namespace Tests\Feature;

use App\Models\User;
use App\UseCases\Contracts\Auth\LoginInterface;
use App\UseCases\Contracts\Auth\RegisterInterface;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * Test register user
     *
     * @return void
     */
    public function test_register_user()
    {
        $registerMock = $this->createMock(RegisterInterface::class);
        $registerMock->expects($this->once())
            ->method('handle')
            ->willReturn(['message' => 'User created successfully']);

        $this->app->instance(RegisterInterface::class, $registerMock);

        $response = $this->postJson('/api/register', [
            'name' => 'Mock User',
            'email' => 'mockuser@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'User created successfully']);
    }

    /**
     * Test login user
     *
     * @return void
     */
    public function test_login_user()
    {
        $loginMock = $this->createMock(LoginInterface::class);
        $loginMock->expects($this->once())
            ->method('handle')
            ->with(['email' => 'test@example.com', 'password' => 'password'])
            ->willReturn(['token' => 'mocked_jwt_token', 'status' => 200]);

        $this->app->instance(LoginInterface::class, $loginMock);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    /**
     * Test logout user
     *
     * @return void
     */
    public function test_logout_user()
    {
        JWTAuth::shouldReceive('setRequest')
            ->withAnyArgs()
            ->andReturnSelf();

        JWTAuth::shouldReceive('parser')
            ->withAnyArgs()
            ->andReturnSelf();

        JWTAuth::shouldReceive('hasToken')
            ->andReturn(true);

        JWTAuth::shouldReceive('getToken')
            ->andReturn('mocked_token');

        JWTAuth::shouldReceive('parseToken')
            ->andReturnSelf();

        JWTAuth::shouldReceive('authenticate')
            ->andReturn((object) ['id' => 1, 'name' => 'Mock User', 'email' => 'mockuser@example.com']);

        JWTAuth::shouldReceive('invalidate')
            ->with('mocked_token')
            ->andReturn(true);

        JWTAuth::shouldReceive('refresh')
            ->andReturn('new_mocked_token');

        $response = $this->postJson('/api/logout', [], [
            'Authorization' => 'Bearer mocked_token',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out',
                'status' => 'success',
            ]);
    }

    /**
     * Test refresh token
     *
     * @return void
     */
    public function test_refresh_token()
    {
        JWTAuth::shouldReceive('refresh')->once()->andReturn('new_token');
        JWTAuth::shouldReceive('user')->once()->andReturn(['id' => 1, 'name' => 'Test User']);

        $response = $this->postJson('/api/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'user', 'authorization']);
    }

    /**
     * Test get authenticated user
     *
     * @return void
     */
    public function test_get_authenticated_user()
    {
        $this->withoutMiddleware();

        $user = User::factory()->make([
            'id' => 1,
            'name' => 'Mock User',
            'email' => 'mockuser@example.com',
        ]);

        $this->actingAs($user);

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'user' => [
                    'id' => 1,
                    'name' => 'Mock User',
                    'email' => 'mockuser@example.com',
                ],
            ]);
    }
}
