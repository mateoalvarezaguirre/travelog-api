<?php

declare(strict_types=1);

namespace Tests\Unit\Profile;

use Src\Profile\Application\UseCases\GetStatsUseCase;
use Src\Profile\Domain\Exceptions\UserNotFoundExceptions;
use Src\Profile\Domain\Repositories\ProfileRepository;
use Src\Profile\Domain\ValueObjects\StatsView;
use Tests\TestCase;

/**
 * @internal
 */
class GetStatsUseCaseTest extends TestCase
{
    public function test_invoke_returns_stats_view_when_user_exists(): void
    {
        // Arrange
        $expectedStats = new StatsView(
            totalDistance: '12,500 km',
            countriesVisited: 15,
            citiesExplored: 42,
            journalsWritten: 8,
            regions: [
                ['name' => 'Europe', 'percentage' => 60],
                ['name' => 'Asia', 'percentage' => 40],
            ],
        );

        $repository = $this->createMock(ProfileRepository::class);
        $repository->expects($this->once())
            ->method('getStatsView')
            ->with(10)
            ->willReturn($expectedStats);

        $useCase = new GetStatsUseCase($repository);

        // Act
        $result = $useCase(10);

        // Assert
        $this->assertInstanceOf(StatsView::class, $result);
        $this->assertSame('12,500 km', $result->totalDistance);
        $this->assertSame(15, $result->countriesVisited);
        $this->assertSame(42, $result->citiesExplored);
        $this->assertSame(8, $result->journalsWritten);
    }

    public function test_invoke_throws_when_user_not_found(): void
    {
        // Arrange
        $repository = $this->createMock(ProfileRepository::class);
        $repository->expects($this->once())
            ->method('getStatsView')
            ->with(999)
            ->willReturn(null);

        $useCase = new GetStatsUseCase($repository);

        // Assert
        $this->expectException(UserNotFoundExceptions::class);

        // Act
        $useCase(999);
    }
}
