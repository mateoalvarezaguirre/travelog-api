<?php

declare(strict_types=1);

namespace Src\Auth\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Src\Auth\Application\UseCases\RegisterUseCase;
use Src\Auth\Domain\Contracts\AuthManagement;
use Src\Auth\Domain\Repositories\UserRepository;
use Src\Auth\Infrastructure\Http\Requests\RegisterRequest;
use Src\Auth\Infrastructure\Http\Resources\AuthUserResource;

readonly class RegisterController
{
    public function __construct(
        private UserRepository $userRepository,
        private AuthManagement $authManagement
    ) {}

    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $useCase = new RegisterUseCase(
            $request->dto(),
            $this->userRepository,
            $this->authManagement
        );

        $authUserDTO = $useCase();

        return response()->json(new AuthUserResource($authUserDTO), 201);
    }
}
