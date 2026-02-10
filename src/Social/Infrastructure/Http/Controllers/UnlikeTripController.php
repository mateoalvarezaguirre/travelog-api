<?php

declare(strict_types=1);

namespace Src\Social\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Social\Application\UseCases\UnlikeTripUseCase;

readonly class UnlikeTripController
{
    public function __construct(
        private UnlikeTripUseCase $useCase,
    ) {}

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $likesCount = ($this->useCase)($id, $request->user()->id);

        return response()->json(['likesCount' => $likesCount]);
    }
}
