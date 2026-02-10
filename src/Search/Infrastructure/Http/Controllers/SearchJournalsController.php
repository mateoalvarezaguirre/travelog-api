<?php

declare(strict_types=1);

namespace Src\Search\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Search\Application\UseCases\SearchJournalsUseCase;
use Src\Trip\Domain\Repositories\TripRepository;
use Src\Trip\Infrastructure\Http\Resources\TripResource;

readonly class SearchJournalsController
{
    public function __construct(
        private SearchJournalsUseCase $useCase,
        private TripRepository $tripRepository,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $query = (string) $request->query('q', '');
        $trips = ($this->useCase)($query);

        $likedTripIds = [];
        $user         = $request->user();
        if ($user !== null && $trips !== []) {
            $tripIds      = array_map(fn ($trip) => $trip->id, $trips);
            $likedTripIds = $this->tripRepository->getLikedTripIds($user->id, $tripIds);
        }

        $data = [];
        foreach ($trips as $trip) {
            $data[] = (new TripResource($trip, in_array($trip->id, $likedTripIds, true)))->jsonSerialize();
        }

        return response()->json($data);
    }
}
