<?php

declare(strict_types=1);

namespace Src\Trip\Application\DTOs\Out;

use Src\Trip\Domain\Entities\TripEntity;

readonly class GetTripResult
{
    public function __construct(
        private TripEntity $trip,
        private bool $isLiked = false,
    ) {}

    public function getTrip(): TripEntity
    {
        return $this->trip;
    }

    public function isLiked(): bool
    {
        return $this->isLiked;
    }
}
