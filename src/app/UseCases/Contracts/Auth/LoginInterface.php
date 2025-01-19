<?php

namespace App\UseCases\Contracts\Auth;

interface LoginInterface
{
    /**
     * Login a user
     *
     * @param array $credentials
     * @return array
     */
    public function handle(array $credentials): array;
}
