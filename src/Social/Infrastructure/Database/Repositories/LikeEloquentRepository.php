<?php

declare(strict_types=1);

namespace Src\Social\Infrastructure\Database\Repositories;

use App\Models\Like;
use Src\Social\Domain\Repositories\LikeRepository;

class LikeEloquentRepository implements LikeRepository
{
    public function like(string $tripId, int $userId): int
    {
        Like::firstOrCreate([
            'trip_id' => $tripId,
            'user_id' => $userId,
        ], [
            'created_at' => now(),
        ]);

        return Like::where('trip_id', $tripId)->count();
    }

    public function unlike(string $tripId, int $userId): int
    {
        Like::where('trip_id', $tripId)
            ->where('user_id', $userId)
            ->delete();

        return Like::where('trip_id', $tripId)->count();
    }
}
