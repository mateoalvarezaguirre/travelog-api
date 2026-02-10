<?php

namespace Src\Auth\Infrastructure\Http\Controllers;

use Src\Auth\Application\UseCases\GoogleAuthUseCase;
use Src\Auth\Domain\Contracts\AuthManagement;
use Src\Auth\Domain\Contracts\GoogleManagement;
use Src\Auth\Domain\Repositories\UserRepository;
use Src\Auth\Infrastructure\Http\Requests\GoogleAuthRequest;
use Src\Auth\Infrastructure\Http\Resources\AuthUserResource;

readonly class GoogleAuthController
{
    public function __construct(
        private GoogleManagement $googleManagement,
        private UserRepository $repository,
        private AuthManagement $authManagement
    ) {}

    public function __invoke(GoogleAuthRequest $request): AuthUserResource
    {
        $useCase = new GoogleAuthUseCase(
            $request->dto(),
            $this->googleManagement,
            $this->repository,
            $this->authManagement
        );

        return new AuthUserResource($useCase());
    }
}
