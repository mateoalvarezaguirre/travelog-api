<?php

declare(strict_types=1);

namespace Src\Auth\Application\DTO\In;

readonly class RegisterDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $username,
    ) {}
}
