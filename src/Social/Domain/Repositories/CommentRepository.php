<?php

declare(strict_types=1);

namespace Src\Social\Domain\Repositories;

use App\Models\TripComment;

interface CommentRepository
{
    /**
     * @return mixed[]
     */
    public function findByTrip(string $tripId): array;

    public function create(string $tripId, int $userId, string $text): TripComment;
}
