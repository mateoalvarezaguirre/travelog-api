<?php

namespace Src\Auth\Application\DTO\In;

readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ){}
}
