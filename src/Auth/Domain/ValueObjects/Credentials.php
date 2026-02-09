<?php

namespace Src\Auth\Domain\ValueObjects;

readonly class Credentials
{
    public function __construct(
        public string $email,
        public string $password
    ){}
}
