<?php

namespace Src\Auth\Application\UseCases;

use Src\Auth\Application\DTO\In\LoginDTO;
use Src\Auth\Application\DTO\Out\AuthUserDTO;
use Src\Auth\Domain\Contracts\AuthManagement;
use Src\Auth\Domain\Exceptions\InvalidCredentialsException;
use Src\Auth\Domain\Repositories\UserRepository;
use Src\Shared\Core\Domain\ValueObjects\Email;

readonly class LoginUseCase
{
    public function __construct(
        private LoginDTO $dto,
        private AuthManagement $authManagement,
        private UserRepository $repository,
    ) {}

    /**
     * @throws InvalidCredentialsException
     */
    public function __invoke(): AuthUserDTO
    {
        $authUser = $this->repository->findByEmail(
            email: new Email($this->dto->email)
        );

        if (! $authUser || ! $authUser->password->check($this->dto->password)) {
            throw new InvalidCredentialsException();
        }

        $authToken = $this->authManagement->getAuthToken($authUser);

        return new AuthUserDTO(
            user: $authUser,
            authToken: $authToken
        );
    }
}
