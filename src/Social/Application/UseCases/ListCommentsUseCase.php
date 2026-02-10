<?php

declare(strict_types=1);

namespace Src\Social\Application\UseCases;

use Src\Social\Domain\Repositories\CommentRepository;

readonly class ListCommentsUseCase
{
    public function __construct(
        private CommentRepository $commentRepository,
    ) {}

    /**
     * @return mixed[]
     */
    public function __invoke(string $tripId): array
    {
        return $this->commentRepository->findByTrip($tripId);
    }
}
