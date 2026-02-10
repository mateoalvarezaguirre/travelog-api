<?php

declare(strict_types=1);

namespace Src\Place\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Place\Application\UseCases\ListPlacesUseCase;
use Src\Place\Infrastructure\Http\Resources\PlaceResource;

readonly class ListPlacesController
{
    public function __construct(
        private ListPlacesUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $places = ($this->useCase)($request->user()->id);

        return response()->json(PlaceResource::collection($places));
    }
}
