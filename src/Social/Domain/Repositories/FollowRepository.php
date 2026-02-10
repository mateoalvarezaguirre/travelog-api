<?php

declare(strict_types=1);

namespace Src\Social\Domain\Repositories;

interface FollowRepository
{
    public function follow(int $followerId, int $followingId): void;

    public function unfollow(int $followerId, int $followingId): void;
}
