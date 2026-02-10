<?php

declare(strict_types=1);

namespace Src\Place\Application\DTOs;

readonly class CreatePlaceDTO
{
    public function __construct(
        public int $userId,
        public string $name,
        public string $country,
        public ?string $date,
        public float $latitude,
        public float $longitude,
        public string $markerType,
        public ?string $image,
    ) {}
}
