<?php

declare(strict_types=1);

namespace Src\Trip\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Trip\Application\DTOs\In\ListPublicTripsDTO;
use Src\Trip\Application\UseCases\ListPublicTripsUseCase;
use Src\Trip\Infrastructure\Http\Resources\TripCollectionResource;

readonly class ListPublicTripsController
{
    public function __construct(
        private ListPublicTripsUseCase $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $dto  = new ListPublicTripsDTO(
            page: (int) $request->query('page', 1),
            search: $request->query('search'),
            tag: $request->query('tag'),
            destination: $request->query('destination'),
            tab: $request->query('tab', 'recent'),
            authUserId: $user?->id,
        );

        $result = ($this->useCase)($dto);

        return response()->json(
            (new TripCollectionResource($result->getPaginator(), $result->getLikedTripIds()))->jsonSerialize()
        );
    }
}
