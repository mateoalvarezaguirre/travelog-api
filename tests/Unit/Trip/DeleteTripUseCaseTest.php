<?php

declare(strict_types=1);

namespace Tests\Unit\Trip;

use Carbon\Carbon;
use Src\Trip\Application\UseCases\DeleteTripUseCase;
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
class DeleteTripUseCaseTest extends TestCase
{
    public function test_invoke_calls_repository_delete_when_owner(): void
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

        $repository = $this->createMock(TripRepository::class);
        $repository->method('findById')->with('trip-uuid')->willReturn($entity);
        $repository->expects($this->once())
            ->method('delete')
            ->with('trip-uuid');

        $useCase = new DeleteTripUseCase($repository);

        $useCase('trip-uuid', 1);
    }
}
