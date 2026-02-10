<?php

declare(strict_types=1);

namespace Src\Trip\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Src\Trip\Application\UseCases\GetTripUseCase;
use Src\Trip\Application\UseCases\UpdateTripUseCase;
use Src\Trip\Infrastructure\Http\Requests\UpdateTripRequest;
use Src\Trip\Infrastructure\Http\Resources\TripEntityResource;

readonly class UpdateTripController
{
    public function __construct(
        private UpdateTripUseCase $useCase,
        private GetTripUseCase $getTripUseCase,
    ) {}

    public function __invoke(UpdateTripRequest $request, string $id): JsonResponse
    {
        $entity = ($this->useCase)($request->dto());
        $result = ($this->getTripUseCase)($entity->getUuid(), $request->user()->id);

        return response()->json(
            (new TripEntityResource($result->getTrip(), $result->isLiked()))->jsonSerialize()
        );
    }
}
