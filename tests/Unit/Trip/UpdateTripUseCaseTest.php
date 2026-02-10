<?php

declare(strict_types=1);

namespace Tests\Unit\Trip;

use Carbon\Carbon;
use Src\Trip\Application\DTOs\In\UpdateTripDTO;
use Src\Trip\Application\UseCases\UpdateTripUseCase;
use Src\Trip\Domain\Entities\TripEntity;
use Src\Trip\Domain\Enums\StatusEnum;
use Src\Trip\Domain\Enums\VisibilityEnum;
use Src\Trip\Domain\Exceptions\TripNotFoundException;
use Src\Trip\Domain\Exceptions\UnauthorizedTripActionException;
use Src\Trip\Domain\Repositories\TripRepository;
use Src\Trip\Domain\ValueObjects\Engagement;
use Src\Trip\Domain\ValueObjects\Owner;
use Tests\TestCase;

/**
 * @internal
 */
class UpdateTripUseCaseTest extends TestCase
{
    public function test_invoke_throws_when_trip_not_found(): void
    {
        // Arrange
        $dto = new UpdateTripDTO(tripId: 'missing-id', ownerId: 1, title: 'New Title');

        $repository = $this->createMock(TripRepository::class);
        $repository->method('findById')->with('missing-id')->willReturn(null);

        $useCase = new UpdateTripUseCase($repository);

        // Assert
        $this->expectException(TripNotFoundException::class);

        // Act
        $useCase($dto);
    }

    public function test_invoke_throws_when_user_is_not_owner(): void
    {
        // Arrange
        $entity = $this->createTripEntity(ownerId: 5);

        $dto = new UpdateTripDTO(tripId: 'trip-1', ownerId: 99, title: 'Hacked');

        $repository = $this->createMock(TripRepository::class);
        $repository->method('findById')->with('trip-1')->willReturn($entity);

        $useCase = new UpdateTripUseCase($repository);

        // Assert
        $this->expectException(UnauthorizedTripActionException::class);

        // Act
        $useCase($dto);
    }

    public function test_invoke_updates_trip_and_returns_updated_entity(): void
    {
        // Arrange
        $originalEntity = $this->createTripEntity(ownerId: 1);
        $updatedEntity  = $this->createTripEntity(ownerId: 1);

        $dto = new UpdateTripDTO(
            tripId: 'trip-1',
            ownerId: 1,
            title: 'Updated Title',
            tags: ['new-tag'],
            imageIds: [100],
        );

        $repository = $this->createMock(TripRepository::class);
        $repository->method('findById')
            ->with('trip-1')
            ->willReturnOnConsecutiveCalls($originalEntity, $updatedEntity);

        $repository->expects($this->once())
            ->method('update')
            ->with('trip-1', $this->isType('array'));

        $repository->expects($this->once())
            ->method('syncTagsByTripId')
            ->with('trip-1', ['new-tag']);

        $repository->expects($this->once())
            ->method('syncMediaByTripId')
            ->with('trip-1', [100]);

        $useCase = new UpdateTripUseCase($repository);

        // Act
        $result = $useCase($dto);

        // Assert
        $this->assertInstanceOf(TripEntity::class, $result);
    }

    private function createTripEntity(int $ownerId): TripEntity
    {
        $entity = new TripEntity(
            title: 'Original Title',
            content: 'Original content',
            owner: new Owner($ownerId),
            status: StatusEnum::DRAFT,
            visibility: VisibilityEnum::PUBLIC,
            engagement: new Engagement(),
            createdAt: Carbon::now(),
        );
        $entity->setId('trip-1');

        return $entity;
    }
}
