<?php

declare(strict_types=1);

namespace Src\Place\Infrastructure\Database\Repositories;

use App\Models\Place;
use Src\Place\Domain\Exceptions\PlaceNotFoundException;
use Src\Place\Domain\Exceptions\UnauthorizedPlaceActionException;
use Src\Place\Domain\Repositories\PlaceRepository;

class PlaceEloquentRepository implements PlaceRepository
{
    /**
     * @return Place[]
     */
    public function listByUserId(int $userId): array
    {
        return Place::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->all();
    }

    public function create(
        int $userId,
        string $name,
        string $country,
        ?string $date,
        float $latitude,
        float $longitude,
        string $markerType,
        ?string $image,
    ): Place {
        return Place::create([
            'user_id'     => $userId,
            'name'        => $name,
            'country'     => $country,
            'date'        => $date,
            'latitude'    => $latitude,
            'longitude'   => $longitude,
            'marker_type' => $markerType,
            'image'       => $image,
        ]);
    }

    public function delete(int $placeId, int $userId): void
    {
        $place = Place::find($placeId);

        if ($place === null) {
            throw new PlaceNotFoundException();
        }

        if ($place->user_id !== $userId) {
            throw new UnauthorizedPlaceActionException();
        }

        $place->delete();
    }
}
