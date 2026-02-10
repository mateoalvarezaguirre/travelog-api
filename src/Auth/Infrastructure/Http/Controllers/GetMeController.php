<?php

declare(strict_types=1);

namespace Src\Auth\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Auth\Application\UseCases\GetMeUseCase;

readonly class GetMeController
{
    public function __construct(
        private GetMeUseCase $getMeUseCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(($this->getMeUseCase)($request->user()));
    }
}
