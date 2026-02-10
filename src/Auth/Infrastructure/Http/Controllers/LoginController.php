<?php

namespace Src\Auth\Infrastructure\Http\Controllers;

use Src\Auth\Application\UseCases\LoginUseCase;
use Src\Auth\Domain\Contracts\AuthManagement;
use Src\Auth\Domain\Exceptions\InvalidCredentialsException;
use Src\Auth\Domain\Repositories\UserRepository;
use Src\Auth\Infrastructure\Http\Requests\LoginRequest;
use Src\Auth\Infrastructure\Http\Resources\AuthUserResource;

readonly class LoginController
{
    public function __construct(
        private AuthManagement $authManagement,
        private UserRepository $repository,
    ) {}

    /**
     * @throws InvalidCredentialsException
     */
    public function __invoke(LoginRequest $request): AuthUserResource
    {
        $useCase = new LoginUseCase(
            $request->dto(),
            $this->authManagement,
            $this->repository
        );

        $authUser = $useCase();

        return new AuthUserResource($authUser);
    }
}
