<?php

declare(strict_types=1);

namespace Tests\Unit\Trip;

use Illuminate\Pagination\LengthAwarePaginator;
use Src\Shared\Core\Domain\ValueObjects\PaginationResult;
use Src\Trip\Application\DTOs\In\ListTripsDTO;
use Src\Trip\Application\UseCases\ListTripsUseCase;
use Src\Trip\Domain\Repositories\TripRepository;
use Tests\TestCase;

/**
 * @internal
 */
class ListTripsUseCaseTest extends TestCase
{
    public function test_invoke_returns_list_trips_result_with_paginator_and_liked_ids(): void
    {
        $items            = [];
        $paginationResult = new PaginationResult(
            items: $items,
            currentPage: 1,
            lastPage: 1,
            perPage: 12,
            total: 0,
        );

        $repository = $this->createMock(TripRepository::class);
        $repository->method('findByOwner')->willReturn($paginationResult);
        $repository->method('getLikedTripIds')->willReturn([]);

        $useCase = new ListTripsUseCase($repository);
        $dto     = new ListTripsDTO(
            ownerId: 1,
            page: 1,
            search: null,
            tag: null,
            status: null,
            tab: 'recent',
        );

        $result = $useCase($dto);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result->getPaginator());
        $this->assertSame([], $result->getLikedTripIds());
    }
}
