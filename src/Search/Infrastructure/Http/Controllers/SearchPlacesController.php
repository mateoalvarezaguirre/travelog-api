<?php

declare(strict_types=1);

namespace Src\Search\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Place\Infrastructure\Http\Resources\PlaceResource;
use Src\Search\Application\UseCases\SearchPlacesUseCase;

readonly class SearchPlacesController
{
    public function __construct(
        private SearchPlacesUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $query  = (string) $request->query('q', '');
        $places = ($this->useCase)($query);

        return response()->json(PlaceResource::collection($places));
    }
}
