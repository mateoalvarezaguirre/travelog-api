<?php

declare(strict_types=1);

namespace Src\Trip\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Trip\Application\DTOs\In\ListTripsDTO;
use Src\Trip\Application\UseCases\ListTripsUseCase;
use Src\Trip\Infrastructure\Http\Resources\TripCollectionResource;

readonly class ListTripsController
{
    public function __construct(
        private ListTripsUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $dto  = new ListTripsDTO(
            ownerId: $user->id,
            page: (int) $request->query('page', 1),
            search: $request->query('search'),
            tag: $request->query('tag'),
            status: $request->query('status'),
            tab: $request->query('tab', 'recent'),
        );

        $result = ($this->useCase)($dto);

        return response()->json(
            (new TripCollectionResource($result->getPaginator(), $result->getLikedTripIds()))->jsonSerialize()
        );
    }
}
