<?php

namespace Src\Auth\Application\UseCases;

use Src\Auth\Application\DTO\In\LoginDTO;
use Src\Auth\Application\DTO\Out\AuthUserDTO;
use Src\Auth\Domain\Contracts\AuthManagement;
use Src\Auth\Domain\Exceptions\InvalidCredentialsException;
use Src\Auth\Domain\ValueObjects\Credentials;

readonly class LoginUseCase
{
    public function __construct(
        private LoginDTO $loginDTO,
        private AuthManagement $authManagement
    ){}

    /**
     * @throws InvalidCredentialsException
     */
    public function __invoke(): AuthUserDTO
    {
        $credentials = new Credentials(
            email: $this->loginDTO->email,
            password: $this->loginDTO->password
        );

        if (!$this->authManagement->attempt($credentials)) {
            throw new InvalidCredentialsException();
        }

        $authUser = $this->authManagement->getAuthUser();

        $authToken = $this->authManagement->getAuthToken();

        return new AuthUserDTO(
            user: $authUser,
            authToken: $authToken
        );
    }
}
