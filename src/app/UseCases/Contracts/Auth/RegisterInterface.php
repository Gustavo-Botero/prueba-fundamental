<?php

namespace App\UseCases\Contracts\Auth;

use App\Http\Requests\Auth\RegisterRequest;

interface RegisterInterface
{
    /**
     * Register a new user
     *
     * @param RegisterRequest $request
     * @return array
     */
    public function handle(RegisterRequest $request): array;
}
