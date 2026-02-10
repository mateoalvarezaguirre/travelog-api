<?php

declare(strict_types=1);

namespace Tests\Unit\Place;

use Src\Place\Application\UseCases\ListPlacesUseCase;
use Src\Place\Domain\Repositories\PlaceRepository;
use Tests\TestCase;

/**
 * @internal
 */
class ListPlacesUseCaseTest extends TestCase
{
    public function test_invoke_returns_places_for_given_user(): void
    {
        // Arrange
        $expectedPlaces = [
            ['id' => 1, 'name' => 'Tokyo Tower', 'country' => 'Japan'],
            ['id' => 2, 'name' => 'Big Ben', 'country' => 'UK'],
        ];

        $repository = $this->createMock(PlaceRepository::class);
        $repository->expects($this->once())
            ->method('listByUserId')
            ->with(5)
            ->willReturn($expectedPlaces);

        $useCase = new ListPlacesUseCase($repository);

        // Act
        $result = $useCase(5);

        // Assert
        $this->assertSame($expectedPlaces, $result);
        $this->assertCount(2, $result);
    }

    public function test_invoke_returns_empty_array_when_no_places(): void
    {
        // Arrange
        $repository = $this->createMock(PlaceRepository::class);
        $repository->expects($this->once())
            ->method('listByUserId')
            ->with(99)
            ->willReturn([]);

        $useCase = new ListPlacesUseCase($repository);

        // Act
        $result = $useCase(99);

        // Assert
        $this->assertSame([], $result);
    }
}
