<?php

declare(strict_types=1);

namespace Src\Trip\Domain\Entities;

readonly class TripLocation
{
    public function __construct(
        public string $tripUuid,
        public string $locationUuid,
    ) {}
}
