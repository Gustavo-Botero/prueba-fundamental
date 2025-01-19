<?php

namespace App\UseCases\Modules\Auth;

use App\UseCases\Contracts\Auth\LoginInterface;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class LoginUsecase implements LoginInterface
{
    /**
     * Login a user
     *
     * @param array $credentials
     * @return array
     */
    public function handle(array $credentials): array
    {
        $token = JWTAuth::attempt($credentials);
        $response = [
            'token' => $token,
            'status' => 200
        ];

        if (!$token) {
            $response = [
                'error' => 'Invalid credentials',
                'status' => 401
            ];
        }

        return $response;
    }
}
