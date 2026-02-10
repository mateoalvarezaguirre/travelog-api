<?php

namespace Src\Auth\Application\UseCases;

use Src\Auth\Application\DTO\In\RegisterDTO;
use Src\Auth\Application\DTO\Out\AuthUserDTO;
use Src\Auth\Domain\Contracts\AuthManagement;
use Src\Auth\Domain\Entities\UserEntity;
use Src\Auth\Domain\Objects\Password;
use Src\Auth\Domain\Repositories\UserRepository;
use Src\Shared\Core\Domain\ValueObjects\Email;

readonly class RegisterUseCase
{
    public function __construct(
        private RegisterDTO $dto,
        private UserRepository $repository,
        private AuthManagement $authManagement
    ) {}

    public function __invoke(): AuthUserDTO
    {
        $user = new UserEntity(
            $this->dto->name,
            new Email($this->dto->email),
            new Password($this->dto->password),
            $this->dto->username
        );

        $this->repository->save($user);

        $authToken = $this->authManagement->getAuthToken($user);

        return new AuthUserDTO(
            user: $user,
            authToken: $authToken
        );
    }
}
