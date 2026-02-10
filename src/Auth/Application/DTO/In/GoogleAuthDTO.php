<?php

namespace Src\Auth\Application\DTO\In;

readonly class GoogleAuthDTO
{
    public function __construct(
        public string $googleId
    ) {}
}
