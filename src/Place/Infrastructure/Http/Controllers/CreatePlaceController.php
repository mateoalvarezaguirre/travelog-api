<?php

declare(strict_types=1);

namespace Src\Place\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Src\Place\Application\UseCases\CreatePlaceUseCase;
use Src\Place\Infrastructure\Http\Requests\StorePlaceRequest;
use Src\Place\Infrastructure\Http\Resources\PlaceResource;

readonly class CreatePlaceController
{
    public function __construct(
        private CreatePlaceUseCase $useCase,
    ) {}

    public function __invoke(StorePlaceRequest $request): JsonResponse
    {
        $place = ($this->useCase)($request->dto());

        return response()->json(
            (new PlaceResource($place))->jsonSerialize(),
            201
        );
    }
}
