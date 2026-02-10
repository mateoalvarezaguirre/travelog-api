<?php

declare(strict_types=1);

namespace Src\Trip\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Src\Trip\Application\UseCases\DeleteTripUseCase;

readonly class DeleteTripController
{
    public function __construct(
        private DeleteTripUseCase $useCase,
    ) {}

    public function __invoke(Request $request, string $id): Response
    {
        ($this->useCase)($id, $request->user()->id);

        return response()->noContent();
    }
}
