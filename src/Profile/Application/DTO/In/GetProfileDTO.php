<?php

declare(strict_types=1);

namespace Src\Profile\Application\DTO\In;

readonly class GetProfileDTO
{
    public function __construct(
        public string $email,
    ) {}
}
