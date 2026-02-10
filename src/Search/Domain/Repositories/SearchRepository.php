<?php

declare(strict_types=1);

namespace Src\Search\Domain\Repositories;

interface SearchRepository
{
    /**
     * Search published public trips by title, content, or location.
     *
     * @return object[]
     */
    public function searchTrips(string $query, int $limit = 50): array;

    /**
     * Search users by name or username.
     *
     * @return object[]
     */
    public function searchUsers(string $query, int $limit = 50): array;

    /**
     * Search places by name or country.
     *
     * @return object[]
     */
    public function searchPlaces(string $query, int $limit = 50): array;
}
