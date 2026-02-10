<?php

declare(strict_types=1);

namespace Src\Trip\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Trip\Application\UseCases\GetTripUseCase;
use Src\Trip\Domain\Exceptions\TripNotFoundException;
use Src\Trip\Infrastructure\Http\Resources\TripEntityResource;

readonly class GetTripController
{
    public function __construct(
        private GetTripUseCase $useCase,
    ) {}

    public function __invoke(Request $request, string $id): JsonResponse
    {
        try {
            $result = ($this->useCase)($id, $request->user()?->id);
        } catch (TripNotFoundException) {
            return response()->json(['message' => 'Bitácora no encontrada'], 404);
        }

        return response()->json(
            (new TripEntityResource($result->getTrip(), $result->isLiked()))->jsonSerialize()
        );
    }
}
