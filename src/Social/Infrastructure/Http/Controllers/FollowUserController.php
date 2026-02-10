<?php

declare(strict_types=1);

namespace Src\Social\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Src\Social\Application\UseCases\FollowUserUseCase;

readonly class FollowUserController
{
    public function __construct(
        private FollowUserUseCase $useCase,
    ) {}

    public function __invoke(Request $request, int $id): Response
    {
        ($this->useCase)($request->user()->id, $id);

        return response()->noContent();
    }
}
