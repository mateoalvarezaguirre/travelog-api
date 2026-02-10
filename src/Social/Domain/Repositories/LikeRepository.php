<?php

declare(strict_types=1);

namespace Src\Social\Domain\Repositories;

interface LikeRepository
{
    public function like(string $tripId, int $userId): int;

    public function unlike(string $tripId, int $userId): int;
}
