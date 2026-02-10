<?php

declare(strict_types=1);

namespace Src\Place\Domain\Repositories;

use App\Models\Place;

interface PlaceRepository
{
    /**
     * @return mixed[]
     */
    public function listByUserId(int $userId): array;

    public function create(
        int $userId,
        string $name,
        string $country,
        ?string $date,
        float $latitude,
        float $longitude,
        string $markerType,
        ?string $image,
    ): Place;

    public function delete(int $placeId, int $userId): void;
}
