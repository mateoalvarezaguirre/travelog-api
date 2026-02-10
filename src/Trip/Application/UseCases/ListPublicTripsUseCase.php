<?php

declare(strict_types=1);

namespace Src\Trip\Application\UseCases;

use Illuminate\Pagination\LengthAwarePaginator;
use Src\Trip\Application\DTOs\In\ListPublicTripsDTO;
use Src\Trip\Application\DTOs\Out\ListTripsResult;
use Src\Trip\Domain\Repositories\TripRepository;
use Src\Trip\Domain\ValueObjects\PublicTripFilters;

readonly class ListPublicTripsUseCase
{
    public function __construct(
        private TripRepository $repository,
    ) {}

    public function __invoke(ListPublicTripsDTO $dto): ListTripsResult
    {
        $filters = new PublicTripFilters(
            page: $dto->page,
            search: $dto->search,
            tag: $dto->tag,
            destination: $dto->destination,
            tab: $dto->tab,
            authUserId: $dto->authUserId,
        );

        $result       = $this->repository->findPublic($filters);
        $items        = $result->getItems();
        $likedTripIds = [];
        if ($dto->authUserId !== null) {
            $tripIds      = array_map(fn ($trip) => $trip->id, $items);
            $likedTripIds = $this->repository->getLikedTripIds($dto->authUserId, $tripIds);
        }
        $paginator = new LengthAwarePaginator(
            $items,
            $result->getTotal(),
            $result->getPerPage(),
            $result->getCurrentPage(),
        );

        return new ListTripsResult($paginator, $likedTripIds);
    }
}
