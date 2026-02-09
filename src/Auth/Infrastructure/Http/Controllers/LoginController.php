<?php

namespace Src\Auth\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Src\Auth\Application\UseCases\LoginUseCase;
use Src\Auth\Domain\Contracts\AuthManagement;
use Src\Auth\Domain\Exceptions\InvalidCredentialsException;
use Src\Auth\Infrastructure\Http\Requests\LoginRequest;
use Src\Auth\Infrastructure\Http\Resources\AuthUserResource;

readonly class LoginController
{
    public function __construct(
        private AuthManagement $authManagement
    ) {}

    /**
     * @throws InvalidCredentialsException
     */
    public function __invoke(LoginRequest $request): AuthUserResource
    {
        $useCase = new LoginUseCase(
            $request->dto(),
            $this->authManagement
        );

        $authUser = $useCase();

        return new AuthUserResource($authUser);
    }
}
