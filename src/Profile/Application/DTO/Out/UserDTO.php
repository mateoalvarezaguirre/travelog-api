<?php

namespace Src\Profile\Application\DTO\Out;

use Src\Profile\Domain\ValueObjects\User;

readonly class UserDTO
{
    public function __construct(
        public User $user,
    ) {}
}
