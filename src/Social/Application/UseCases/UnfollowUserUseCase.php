<?php

declare(strict_types=1);

namespace Src\Social\Application\UseCases;

use Src\Social\Domain\Repositories\FollowRepository;

readonly class UnfollowUserUseCase
{
    public function __construct(
        private FollowRepository $followRepository,
    ) {}

    public function __invoke(int $followerId, int $followingId): void
    {
        $this->followRepository->unfollow($followerId, $followingId);
    }
}
