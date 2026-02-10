<?php

declare(strict_types=1);

namespace Tests\Unit\Trip;

use Src\Trip\Application\DTOs\In\CreateTripDTO;
use Src\Trip\Application\UseCases\CreateTripUseCase;
use Src\Trip\Domain\Entities\TripEntity;
use Src\Trip\Domain\Enums\StatusEnum;
use Src\Trip\Domain\Enums\VisibilityEnum;
use Src\Trip\Domain\Repositories\TripRepository;
use Src\Trip\Domain\ValueObjects\Engagement;
use Src\Trip\Domain\ValueObjects\Owner;
use Tests\TestCase;

/**
 * @internal
 */
class CreateTripUseCaseTest extends TestCase
{
    public function test_invoke_creates_draft_trip_and_returns_entity(): void
    {
        // Arrange
        $dto = new CreateTripDTO(
            title: 'My Trip',
            content: '<p>Trip content</p>',
            ownerId: 1,
            date: '2026-01-15',
            location: 'Paris',
            latitude: 48.8566,
            longitude: 2.3522,
            tags: ['travel', 'europe'],
            status: 'draft',
            isPublic: true,
            imageIds: [10, 20],
        );

        $savedEntity = new TripEntity(
            title: 'My Trip',
            content: '<p>Trip content</p>',
            owner: new Owner(1),
            status: StatusEnum::DRAFT,
            visibility: VisibilityEnum::PUBLIC,
            engagement: new Engagement(),
            createdAt: now(),
        );
        $savedEntity->setId('some-uuid');

        $repository = $this->createMock(TripRepository::class);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(TripEntity::class))
            ->willReturn($savedEntity);

        $useCase = new CreateTripUseCase($repository);

        // Act
        $result = $useCase($dto);

        // Assert
        $this->assertInstanceOf(TripEntity::class, $result);
        $this->assertSame('My Trip', $result->title);
        $this->assertSame(StatusEnum::DRAFT, $result->status);
        $this->assertSame(VisibilityEnum::PUBLIC, $result->visibility);
    }

    public function test_invoke_creates_published_trip_with_published_at(): void
    {
        // Arrange
        $dto = new CreateTripDTO(
            title: 'Published Trip',
            content: 'Content here',
            ownerId: 2,
            status: 'published',
            isPublic: false,
        );

        $repository = $this->createMock(TripRepository::class);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(fn (TripEntity $trip): bool => $trip->status === StatusEnum::PUBLISHED
                    && $trip->visibility                                        === VisibilityEnum::PRIVATE
                    && $trip->publishedAt !== null))
            ->willReturnCallback(fn (TripEntity $trip) => $trip);

        $useCase = new CreateTripUseCase($repository);

        // Act
        $result = $useCase($dto);

        // Assert
        $this->assertSame(StatusEnum::PUBLISHED, $result->status);
        $this->assertNotNull($result->publishedAt);
    }
}
