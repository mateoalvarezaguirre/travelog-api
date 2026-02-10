<?php

declare(strict_types=1);

namespace Tests\Unit\Trip;

use Src\Shared\Core\Domain\ValueObjects\PaginationResult;
use Src\Trip\Application\DTOs\In\ListPublicTripsDTO;
use Src\Trip\Application\DTOs\Out\ListTripsResult;
use Src\Trip\Application\UseCases\ListPublicTripsUseCase;
use Src\Trip\Domain\Repositories\TripRepository;
use Tests\TestCase;

/**
 * @internal
 */
class ListPublicTripsUseCaseTest extends TestCase
{
    public function test_invoke_returns_list_trips_result_without_auth_user(): void
    {
        // Arrange
        $dto = new ListPublicTripsDTO(page: 1, tab: 'recent');

        $paginationResult = new PaginationResult(
            items: [],
            currentPage: 1,
            lastPage: 1,
            perPage: 12,
            total: 0,
        );

        $repository = $this->createMock(TripRepository::class);
        $repository->expects($this->once())
            ->method('findPublic')
            ->willReturn($paginationResult);

        $repository->expects($this->never())
            ->method('getLikedTripIds');

        $useCase = new ListPublicTripsUseCase($repository);

        // Act
        $result = $useCase($dto);

        // Assert
        $this->assertInstanceOf(ListTripsResult::class, $result);
        $this->assertSame([], $result->getLikedTripIds());
    }

    public function test_invoke_fetches_liked_trip_ids_for_authenticated_user(): void
    {
        // Arrange
        $item     = new \stdClass();
        $item->id = 'trip-abc';

        $paginationResult = new PaginationResult(
            items: [$item],
            currentPage: 1,
            lastPage: 1,
            perPage: 12,
            total: 1,
        );

        $dto = new ListPublicTripsDTO(page: 1, tab: 'recent', authUserId: 42);

        $repository = $this->createMock(TripRepository::class);
        $repository->expects($this->once())
            ->method('findPublic')
            ->willReturn($paginationResult);

        $repository->expects($this->once())
            ->method('getLikedTripIds')
            ->with(42, ['trip-abc'])
            ->willReturn(['trip-abc']);

        $useCase = new ListPublicTripsUseCase($repository);

        // Act
        $result = $useCase($dto);

        // Assert
        $this->assertInstanceOf(ListTripsResult::class, $result);
        $this->assertSame(['trip-abc'], $result->getLikedTripIds());
    }
}
