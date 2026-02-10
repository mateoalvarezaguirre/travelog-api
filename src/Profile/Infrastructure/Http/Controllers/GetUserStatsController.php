<?php

declare(strict_types=1);

namespace Src\Profile\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Src\Profile\Application\UseCases\GetUserStatsUseCase;
use Src\Profile\Domain\Exceptions\UserNotFoundExceptions;

readonly class GetUserStatsController
{
    public function __construct(
        private GetUserStatsUseCase $useCase,
    ) {}

    public function __invoke(string $username): JsonResponse
    {
        try {
            $stats = ($this->useCase)($username);
        } catch (UserNotFoundExceptions) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($stats->toArray());
    }
}
