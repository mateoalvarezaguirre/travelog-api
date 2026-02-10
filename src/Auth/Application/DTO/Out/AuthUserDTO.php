<?php

namespace Src\Auth\Application\DTO\Out;

use Src\Auth\Domain\Entities\UserEntity;
use Src\Auth\Domain\ValueObjects\AuthToken;

readonly class AuthUserDTO
{
    public function __construct(
        public UserEntity $user,
        public AuthToken $authToken,
    ) {}
}
