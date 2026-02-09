<?php

namespace Src\Auth\Domain\ValueObjects;

readonly class AuthToken
{
    public function __construct(
        public string $value
    ){}
}
