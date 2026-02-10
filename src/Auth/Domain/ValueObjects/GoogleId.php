<?php

declare(strict_types=1);

namespace Src\Auth\Domain\ValueObjects;

readonly class GoogleId
{
    public function __construct(
        public string $value
    ) {}
}
