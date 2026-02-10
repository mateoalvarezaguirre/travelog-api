<?php

namespace Src\Profile\Application\DTO\In;

readonly class GetProfileDTO
{
    public function __construct(
        public string $email,
    ) {}
}
