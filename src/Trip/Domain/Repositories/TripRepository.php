<?php

declare(strict_types=1);

namespace Src\Trip\Domain\Repositories;

use Src\Shared\Core\Domain\ValueObjects\PaginationResult;
use Src\Trip\Domain\Entities\TripEntity;
use Src\Trip\Domain\ValueObjects\PublicTripFilters;
use Src\Trip\Domain\ValueObjects\TripFilters;

interface TripRepository
{
    public function findById(string $id): ?TripEntity;

    public function findByOwner(int $ownerId, TripFilters $filters): PaginationResult;

    public function findPublic(PublicTripFilters $filters): PaginationResult;

    public function save(TripEntity $trip): TripEntity;

    public function update(string $id, array $data): TripEntity;

    public function delete(string $id): void;

    /**
     * @param string[] $tripIds
     *
     * @return string[]
     */
    public function getLikedTripIds(int $userId, array $tripIds): array;

    public function isLikedByUser(string $tripId, int $userId): bool;

    /**
     * @param string[] $tagNames
     */
    public function syncTagsByTripId(string $tripId, array $tagNames): void;

    /**
     * @param int[] $mediaIds
     */
    public function syncMediaByTripId(string $tripId, array $mediaIds): void;
}
