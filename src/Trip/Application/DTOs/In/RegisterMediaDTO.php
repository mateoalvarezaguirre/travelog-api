<?php

declare(strict_types=1);

namespace Src\Trip\Application\DTOs\In;

readonly class RegisterMediaDTO
{
    public function __construct(
        public int $uploadedBy,
        public string $url,
        public ?string $caption = null,
    ) {}
}
