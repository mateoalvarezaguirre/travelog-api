<?php

declare(strict_types=1);

namespace Src\Social\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Src\Social\Application\UseCases\AddCommentUseCase;
use Src\Social\Infrastructure\Http\Requests\StoreCommentRequest;
use Src\Social\Infrastructure\Http\Resources\CommentResource;

readonly class AddCommentController
{
    public function __construct(
        private AddCommentUseCase $useCase,
    ) {}

    public function __invoke(StoreCommentRequest $request, string $id): JsonResponse
    {
        $comment = ($this->useCase)(
            $id,
            $request->user()->id,
            $request->input('text'),
        );

        return response()->json(
            (new CommentResource($comment))->jsonSerialize(),
            201
        );
    }
}
