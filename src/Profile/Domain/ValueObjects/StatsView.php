<?php

declare(strict_types=1);

namespace Src\Profile\Domain\ValueObjects;

readonly class StatsView
{
    /**
     * @param array<int, array{name: string, percentage: int}> $regions
     */
    public function __construct(
        public string $totalDistance,
        public int $countriesVisited,
        public int $citiesExplored,
        public int $journalsWritten,
        public array $regions,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'totalDistance'    => $this->totalDistance,
            'countriesVisited' => $this->countriesVisited,
            'citiesExplored'   => $this->citiesExplored,
            'journalsWritten'  => $this->journalsWritten,
            'regions'          => $this->regions,
        ];
    }
}
