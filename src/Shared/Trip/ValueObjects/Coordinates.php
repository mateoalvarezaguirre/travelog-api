<?php

declare(strict_types=1);

namespace Src\Shared\Trip\ValueObjects;

class Coordinates
{
    public function __construct(
        public readonly float $lat,
        public readonly float $lng,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            lat: (float) $data['lat'],
            lng: (float) $data['lng'],
        );
    }

    public function toArray(): array
    {
        return ['lat' => $this->lat, 'lng' => $this->lng];
    }
}
