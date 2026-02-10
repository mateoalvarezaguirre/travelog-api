<?php

declare(strict_types=1);

namespace Tests\Unit\Search;

use Src\Search\Application\UseCases\SearchJournalsUseCase;
use Src\Search\Domain\Repositories\SearchRepository;
use Tests\TestCase;

/**
 * @internal
 */
class SearchJournalsUseCaseTest extends TestCase
{
    public function test_invoke_delegates_to_repository_with_default_limit(): void
    {
        // Arrange
        $expectedResults = [
            ['id' => 'trip-1', 'title' => 'Paris Adventure'],
            ['id' => 'trip-2', 'title' => 'Paris Food Tour'],
        ];

        $repository = $this->createMock(SearchRepository::class);
        $repository->expects($this->once())
            ->method('searchTrips')
            ->with('Paris', 50)
            ->willReturn($expectedResults);

        $useCase = new SearchJournalsUseCase($repository);

        // Act
        $result = $useCase('Paris');

        // Assert
        $this->assertSame($expectedResults, $result);
        $this->assertCount(2, $result);
    }

    public function test_invoke_returns_empty_array_when_no_results(): void
    {
        // Arrange
        $repository = $this->createMock(SearchRepository::class);
        $repository->expects($this->once())
            ->method('searchTrips')
            ->with('nonexistent query', 50)
            ->willReturn([]);

        $useCase = new SearchJournalsUseCase($repository);

        // Act
        $result = $useCase('nonexistent query');

        // Assert
        $this->assertSame([], $result);
    }
}
