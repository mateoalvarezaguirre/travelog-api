<?php

declare(strict_types=1);

namespace Tests\Unit\Social;

use Src\Social\Application\UseCases\FollowUserUseCase;
use Src\Social\Domain\Exceptions\CannotFollowSelfException;
use Src\Social\Domain\Repositories\FollowRepository;
use Tests\TestCase;

/**
 * @internal
 */
class FollowUserUseCaseTest extends TestCase
{
    public function test_invoke_throws_when_following_self(): void
    {
        $repository = $this->createMock(FollowRepository::class);
        $repository->expects($this->never())->method('follow');

        $useCase = new FollowUserUseCase($repository);

        $this->expectException(CannotFollowSelfException::class);

        $useCase(1, 1);
    }

    public function test_invoke_calls_repository_when_not_self(): void
    {
        $repository = $this->createMock(FollowRepository::class);
        $repository->expects($this->once())
            ->method('follow')
            ->with(1, 2);

        $useCase = new FollowUserUseCase($repository);

        $useCase(1, 2);
    }
}
