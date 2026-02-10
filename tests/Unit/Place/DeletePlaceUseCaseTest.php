<?php

declare(strict_types=1);

namespace Tests\Unit\Place;

use Src\Place\Application\UseCases\DeletePlaceUseCase;
use Src\Place\Domain\Exceptions\PlaceNotFoundException;
use Src\Place\Domain\Repositories\PlaceRepository;
use Tests\TestCase;

/**
 * @internal
 */
class DeletePlaceUseCaseTest extends TestCase
{
    public function test_invoke_throws_place_not_found_when_repository_throws(): void
    {
        $repository = $this->createMock(PlaceRepository::class);
        $repository->method('delete')->willThrowException(new PlaceNotFoundException());

        $useCase = new DeletePlaceUseCase($repository);

        $this->expectException(PlaceNotFoundException::class);

        $useCase(99, 1);
    }

    public function test_invoke_calls_repository_delete(): void
    {
        $repository = $this->createMock(PlaceRepository::class);
        $repository->expects($this->once())
            ->method('delete')
            ->with(1, 1);

        $useCase = new DeletePlaceUseCase($repository);

        $useCase(1, 1);
    }
}
