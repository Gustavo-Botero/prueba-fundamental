<?php

namespace App\UseCases\Contracts\Auth;

use Illuminate\Http\JsonResponse;

interface LoginInterface
{
    /**
     * Login a user
     *
     * @param array $credentials
     * @return JsonResponse
     */
    public function handle(array $credentials): JsonResponse;
}
