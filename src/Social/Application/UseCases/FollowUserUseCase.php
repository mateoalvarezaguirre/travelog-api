<?php

declare(strict_types=1);

namespace Src\Social\Application\UseCases;

use Src\Social\Domain\Exceptions\CannotFollowSelfException;
use Src\Social\Domain\Repositories\FollowRepository;

readonly class FollowUserUseCase
{
    public function __construct(
        private FollowRepository $followRepository,
    ) {}

    public function __invoke(int $followerId, int $followingId): void
    {
        if ($followerId === $followingId) {
            throw new CannotFollowSelfException();
        }

        $this->followRepository->follow($followerId, $followingId);
    }
}
