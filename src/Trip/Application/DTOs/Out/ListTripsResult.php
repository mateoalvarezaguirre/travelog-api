<?php

declare(strict_types=1);

namespace Src\Trip\Application\DTOs\Out;

use App\Models\Trip;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

readonly class ListTripsResult
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
     * @return LengthAwarePaginator<int, Trip>
     */
    public function getPaginator(): LengthAwarePaginator
    {
        return $this->paginator;
    }

    /**
     * @return string[]
     */
    public function getLikedTripIds(): array
    {
        return $this->likedTripIds;
    }
}
