<?php

declare(strict_types=1);

namespace Src\Auth\Application\DTO\In;

readonly class GoogleAuthDTO
{
    public function __construct(
        public string $googleId
    ) {}
}
