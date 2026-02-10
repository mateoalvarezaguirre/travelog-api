<?php

declare(strict_types=1);

namespace Src\Auth\Application\UseCases;

use Src\Auth\Application\DTO\In\GoogleAuthDTO;
use Src\Auth\Application\DTO\Out\AuthUserDTO;
use Src\Auth\Domain\Contracts\AuthManagement;
use Src\Auth\Domain\Contracts\GoogleManagement;
use Src\Auth\Domain\Repositories\UserRepository;
use Src\Auth\Domain\ValueObjects\GoogleId;

readonly class GoogleAuthUseCase
{
    public function __construct(
        private GoogleAuthDTO $dto,
        private GoogleManagement $googleManagement,
        private UserRepository $repository,
        private AuthManagement $authManagement
    ) {}

    public function __invoke(): AuthUserDTO
    {
        $user = $this->googleManagement->getUserByGoogleId(
            new GoogleId($this->dto->googleId),
        );

        $this->repository->saveByEmail($user);

        $authToken = $this->authManagement->getAuthToken($user);

        return new AuthUserDTO(
            user: $user,
            authToken: $authToken
        );
    }
}
