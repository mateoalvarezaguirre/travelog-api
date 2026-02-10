<?php

declare(strict_types=1);

namespace Src\Social\Application\UseCases;

use App\Models\TripComment;
use Src\Social\Domain\Repositories\CommentRepository;

readonly class AddCommentUseCase
{
    public function __construct(
        private CommentRepository $commentRepository,
    ) {}

    public function __invoke(string $tripId, int $userId, string $text): TripComment
    {
        return $this->commentRepository->create($tripId, $userId, $text);
    }
}
