<?php

declare(strict_types=1);

namespace Src\Trip\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Src\Trip\Application\UseCases\CreateTripUseCase;
use Src\Trip\Application\UseCases\GetTripUseCase;
use Src\Trip\Infrastructure\Http\Requests\StoreTripRequest;
use Src\Trip\Infrastructure\Http\Resources\TripEntityResource;

readonly class CreateTripController
{
    public function __construct(
        private CreateTripUseCase $useCase,
        private GetTripUseCase $getTripUseCase,
    ) {}

    public function __invoke(StoreTripRequest $request): JsonResponse
    {
        $entity = ($this->useCase)($request->dto());
        $result = ($this->getTripUseCase)($entity->getUuid(), $request->user()->id);

        return response()->json(
            (new TripEntityResource($result->getTrip(), $result->isLiked()))->jsonSerialize(),
            201
        );
    }
}
