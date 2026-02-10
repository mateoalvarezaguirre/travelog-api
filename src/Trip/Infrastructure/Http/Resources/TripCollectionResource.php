<?php

declare(strict_types=1);

namespace Src\Trip\Infrastructure\Http\Resources;

use App\Models\Trip;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

readonly class TripCollectionResource implements \JsonSerializable
{
    /**
     * @param LengthAwarePaginator<int, Trip> $paginator
     * @param string[] $likedTripIds
     */
    public function __construct(
        private LengthAwarePaginator $paginator,
        private array $likedTripIds = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $data = [];
        foreach ($this->paginator->items() as $trip) {
            $isLiked = in_array($trip->id, $this->likedTripIds);
            $data[]  = (new TripResource($trip, $isLiked))->jsonSerialize();
        }

        return [
            'data' => $data,
            'meta' => [
                'currentPage' => $this->paginator->currentPage(),
                'lastPage'    => $this->paginator->lastPage(),
                'perPage'     => $this->paginator->perPage(),
                'total'       => $this->paginator->total(),
            ],
        ];
    }
}
