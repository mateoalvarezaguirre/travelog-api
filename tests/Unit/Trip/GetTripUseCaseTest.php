<?php

declare(strict_types=1);

namespace Tests\Unit\Trip;

use Carbon\Carbon;
use Src\Trip\Application\DTOs\Out\GetTripResult;
use Src\Trip\Application\UseCases\GetTripUseCase;
use Src\Trip\Domain\Entities\TripEntity;
use Src\Trip\Domain\Enums\StatusEnum;
use Src\Trip\Domain\Enums\VisibilityEnum;
use Src\Trip\Domain\Exceptions\TripNotFoundException;
use Src\Trip\Domain\Repositories\TripRepository;
use Src\Trip\Domain\ValueObjects\Engagement;
use Src\Trip\Domain\ValueObjects\Owner;
use Tests\TestCase;

/**
 * @internal
 */
class GetTripUseCaseTest extends TestCase
{
    public function test_invoke_throws_when_trip_not_found(): void
    {
        $repository = $this->createMock(TripRepository::class);
        $repository->method('findById')->willReturn(null);
        $repository->expects($this->never())->method('isLikedByUser');

        $useCase = new GetTripUseCase($repository);

        $this->expectException(TripNotFoundException::class);

        $useCase('missing-id', null);
    }

    public function test_invoke_returns_result_with_entity_and_is_liked(): void
    {
        $owner  = new Owner(1);
        $entity = new TripEntity(
            title: 'Title',
            content: 'Content',
            owner: $owner,
            status: StatusEnum::DRAFT,
            visibility: VisibilityEnum::PRIVATE,
            engagement: new Engagement(),
            createdAt: Carbon::now(),
            publishedAt: null,
        );
        $entity->setUuid('trip-123');

        $repository = $this->createMock(TripRepository::class);
        $repository->method('findById')->willReturn($entity);
        $repository->method('isLikedByUser')->with('trip-123', 1)->willReturn(true);

        $useCase = new GetTripUseCase($repository);

        $result = $useCase('trip-123', 1);

        $this->assertInstanceOf(GetTripResult::class, $result);
        $this->assertSame($entity, $result->getTrip());
        $this->assertTrue($result->isLiked());
    }
}
