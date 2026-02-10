<?php

declare(strict_types=1);

namespace Src\Profile\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Profile\Application\UseCases\GetStatsUseCase;
use Src\Profile\Domain\Exceptions\UserNotFoundExceptions;

readonly class GetStatsController
{
    public function __construct(
        private GetStatsUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $stats = ($this->useCase)($request->user()->id);
        } catch (UserNotFoundExceptions) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($stats->toArray());
    }
}
