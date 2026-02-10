<?php

declare(strict_types=1);

namespace Src\Social\Application\UseCases;

use Src\Social\Domain\Repositories\LikeRepository;

readonly class UnlikeTripUseCase
{
    public function __construct(
        private LikeRepository $likeRepository,
    ) {}

    public function __invoke(string $tripId, int $userId): int
    {
        return $this->likeRepository->unlike($tripId, $userId);
    }
}
