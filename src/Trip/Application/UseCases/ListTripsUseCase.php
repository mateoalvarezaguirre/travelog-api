<?php

declare(strict_types=1);

namespace Src\Trip\Application\UseCases;

use Illuminate\Pagination\LengthAwarePaginator;
use Src\Trip\Application\DTOs\In\ListTripsDTO;
use Src\Trip\Application\DTOs\Out\ListTripsResult;
use Src\Trip\Domain\Repositories\TripRepository;
use Src\Trip\Domain\ValueObjects\TripFilters;

readonly class ListTripsUseCase
{
    public function __construct(
        private TripRepository $repository,
    ) {}

    public function __invoke(ListTripsDTO $dto): ListTripsResult
    {
        $filters = new TripFilters(
            page: $dto->page,
            search: $dto->search,
            tag: $dto->tag,
            status: $dto->status,
            tab: $dto->tab,
        );

        $result       = $this->repository->findByOwner($dto->ownerId, $filters);
        $items        = $result->getItems();
        $tripIds      = array_map(fn ($trip) => $trip->id, $items);
        $likedTripIds = $this->repository->getLikedTripIds($dto->ownerId, $tripIds);
        $paginator    = new LengthAwarePaginator(
            $items,
            $result->getTotal(),
            $result->getPerPage(),
            $result->getCurrentPage(),
        );

        return new ListTripsResult($paginator, $likedTripIds);
    }
}
