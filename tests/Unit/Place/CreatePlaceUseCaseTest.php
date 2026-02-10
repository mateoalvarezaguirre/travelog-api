<?php

declare(strict_types=1);

namespace Tests\Unit\Place;

use App\Models\Place;
use Src\Place\Application\DTOs\CreatePlaceDTO;
use Src\Place\Application\UseCases\CreatePlaceUseCase;
use Src\Place\Domain\Repositories\PlaceRepository;
use Tests\TestCase;

/**
 * @internal
 */
class CreatePlaceUseCaseTest extends TestCase
{
    public function test_invoke_delegates_to_repository_and_returns_created_place(): void
    {
        // Arrange
        $dto = new CreatePlaceDTO(
            userId: 1,
            name: 'Eiffel Tower',
            country: 'France',
            date: '2026-03-15',
            latitude: 48.8584,
            longitude: 2.2945,
            markerType: 'attraction',
            image: 'https://example.com/eiffel.jpg',
        );

        $expectedPlace = Place::factory()->create([
            'id'      => 1,
            'name'    => 'Eiffel Tower',
            'country' => 'France',
        ]);

        $repository = $this->createMock(PlaceRepository::class);
        $repository->expects($this->once())
            ->method('create')
            ->with(
                1,
                'Eiffel Tower',
                'France',
                '2026-03-15',
                48.8584,
                2.2945,
                'attraction',
                'https://example.com/eiffel.jpg',
            )
            ->willReturn($expectedPlace);

        $useCase = new CreatePlaceUseCase($repository);

        // Act
        $result = $useCase($dto);

        // Assert
        $this->assertSame($expectedPlace->id, $result->id);
    }

    public function test_invoke_passes_null_for_optional_fields(): void
    {
        // Arrange
        $dto = new CreatePlaceDTO(
            userId: 2,
            name: 'Mount Fuji',
            country: 'Japan',
            date: null,
            latitude: 35.3606,
            longitude: 138.7274,
            markerType: 'nature',
            image: null,
        );

        $repository = $this->createMock(PlaceRepository::class);
        $repository->expects($this->once())
            ->method('create')
            ->with(2, 'Mount Fuji', 'Japan', null, 35.3606, 138.7274, 'nature', null)
            ->willReturn(Place::factory()->create([
                'id'      => 2,
                'name'    => 'Mount Fuji',
                'country' => 'Japan',
            ]));

        $useCase = new CreatePlaceUseCase($repository);

        // Act
        $result = $useCase($dto);

        // Assert
        $this->assertSame(2, $result->id);
    }
}
