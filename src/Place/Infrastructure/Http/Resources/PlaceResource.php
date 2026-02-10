<?php

declare(strict_types=1);

namespace Src\Place\Infrastructure\Http\Resources;

use App\Models\Place;
use App\Models\Trip;

readonly class PlaceResource implements \JsonSerializable
{
    public function __construct(
        private Place $place,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $journalCount = Trip::where('owner_id', $this->place->user_id)
            ->where('status', 'published')
            ->where(function ($q): void {
                $q->where('location', 'like', '%' . $this->place->name . '%');
            })
            ->count();

        return [
            'id'          => $this->place->id,
            'name'        => $this->place->name,
            'country'     => $this->place->country,
            'date'        => $this->place->date ?: null,
            'coordinates' => [
                'lat' => $this->place->latitude,
                'lng' => $this->place->longitude,
            ],
            'markerType'   => $this->place->marker_type,
            'journalCount' => $journalCount,
            'image'        => $this->place->image,
        ];
    }

    /**
     * @param Place[] $places
     *
     * @return array<int, array<string, mixed>>
     */
    public static function collection(array $places): array
    {
        return array_map(
            fn (Place $place) => (new self($place))->jsonSerialize(),
            $places
        );
    }
}
