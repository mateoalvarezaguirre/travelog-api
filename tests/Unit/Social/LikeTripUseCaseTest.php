<?php

declare(strict_types=1);

namespace Tests\Unit\Social;

use Src\Social\Application\UseCases\LikeTripUseCase;
use Src\Social\Domain\Repositories\LikeRepository;
use Tests\TestCase;

/**
 * @internal
 */
class LikeTripUseCaseTest extends TestCase
{
    public function test_invoke_delegates_to_repository_and_returns_like_count(): void
    {
        // Arrange
        $repository = $this->createMock(LikeRepository::class);
        $repository->expects($this->once())
            ->method('like')
            ->with('trip-123', 7)
            ->willReturn(15);

        $useCase = new LikeTripUseCase($repository);

        // Act
        $result = $useCase('trip-123', 7);

        // Assert
        $this->assertSame(15, $result);
    }
}
