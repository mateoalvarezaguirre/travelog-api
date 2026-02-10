<?php

declare(strict_types=1);

namespace Src\Social\Infrastructure\Database\Repositories;

use App\Models\Follow;
use Src\Social\Domain\Repositories\FollowRepository;

class FollowEloquentRepository implements FollowRepository
{
    public function follow(int $followerId, int $followingId): void
    {
        Follow::firstOrCreate([
            'follower_id'  => $followerId,
            'following_id' => $followingId,
        ], [
            'created_at' => now(),
        ]);
    }

    public function unfollow(int $followerId, int $followingId): void
    {
        Follow::where('follower_id', $followerId)
            ->where('following_id', $followingId)
            ->delete();
    }

    public function isFollowing(int $followerId, int $followingId): bool
    {
        return Follow::where('follower_id', $followerId)
            ->where('following_id', $followingId)
            ->exists();
    }
}
