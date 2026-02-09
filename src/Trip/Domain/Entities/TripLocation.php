<?php

namespace Src\Trip\Domain\Entities;

class TripLocation
{
    private string $uuid;

    public function __construct(
        public readonly string $tripUuid,
        public readonly string $locationUuid,
    ) {}
}
