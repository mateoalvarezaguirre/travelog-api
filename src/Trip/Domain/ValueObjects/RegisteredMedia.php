<?php

declare(strict_types=1);

namespace Src\Trip\Domain\ValueObjects;

readonly class RegisteredMedia
{
    public function __construct(
        public int $id,
        public string $url,
    ) {}
}
