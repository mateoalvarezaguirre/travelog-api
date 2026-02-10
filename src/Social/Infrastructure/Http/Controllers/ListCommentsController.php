<?php

declare(strict_types=1);

namespace Src\Social\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Src\Social\Application\UseCases\ListCommentsUseCase;
use Src\Social\Infrastructure\Http\Resources\CommentResource;

readonly class ListCommentsController
{
    public function __construct(
        private ListCommentsUseCase $useCase,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        $comments = ($this->useCase)($id);

        return response()->json(CommentResource::collection($comments));
    }
}
