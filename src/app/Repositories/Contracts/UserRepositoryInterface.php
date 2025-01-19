<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    /**
     * Create a new user
     *
     * @param Request $data
     * @return User
     */
    public function create(array $data): User;
}
