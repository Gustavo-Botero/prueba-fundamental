<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\UseCases\Contracts\Auth\LoginInterface;
use App\UseCases\Contracts\Auth\RegisterInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * @param RegisterRequest $request
     * @param RegisterInterface $register
     * @return JsonResponse
     */
    public function register(RegisterRequest $request, RegisterInterface $register): JsonResponse
    {
        return response()->json($register->handle($request), 200);
    }

    /**
     * Login a user
     *
     * @param LoginRequest $request
     * @param LoginInterface $login
     * @return JsonResponse
     */
    public function login(LoginRequest $request, LoginInterface $login): JsonResponse
    {
        $response = $login->handle($request->only('email', 'password'));

        return response()->json(['token' => $response['token']], $response['status']);
    }

    /**
     * Logout a user
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Successfully logged out', 'status' => 'success']);
    }

    /**
     * Refresh token
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'user' => JWTAuth::user(),
            'authorization' => [
                'token' => JWTAuth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    /**
     * Get the authenticated user
     *
     * @return JsonResponse
     */
    public function user(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'user' => $user,
        ]);
    }
}
