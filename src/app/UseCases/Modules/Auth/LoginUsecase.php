<?php

namespace App\UseCases\Modules\Auth;

use App\UseCases\Contracts\Auth\LoginInterface;
use Illuminate\Http\JsonResponse;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class LoginUsecase implements LoginInterface
{
    /**
     * Login a user
     *
     * @param array $credentials
     * @return JsonResponse
     */
    public function handle(array $credentials): JsonResponse
    {
        $token = JWTAuth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'error' => 'Invalid credentials',
            ], 401);
        }

        return response()->json([
            'token' => $token,
        ], 200);
    }
}
