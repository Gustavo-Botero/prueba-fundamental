<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;

class AuthenticationExceptionHandler
{
    /**
     * Handle the authentication exception.
     *
     * @param AuthenticationException $exception
     * @return JsonResponse
     */
    public static function handle(AuthenticationException $exception): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthenticated',
        ], 401);
    }
}
