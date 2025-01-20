<?php

namespace App\UseCases\Modules\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\UseCases\Contracts\Auth\RegisterInterface;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class RegisterUsecase implements RegisterInterface
{
    /**
     * UserRepositoryInterface instance
     *
     * @var UserRepositoryInterface
     */
    protected UserRepositoryInterface $userRepository;

    /**
     * RegisterUsecase constructor
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user
     *
     * @param RegisterRequest $request
     * @return array
     */
    public function handle(RegisterRequest $request): array
    {
        $user = $this->userRepository->create($request->validated());

        $token = JWTAuth::fromUser($user);

        return [
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'Bearer'
            ]
        ];
    }
}
