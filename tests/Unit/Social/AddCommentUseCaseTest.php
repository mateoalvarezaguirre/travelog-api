<?php

declare(strict_types=1);

namespace Tests\Unit\Social;

use App\Models\Trip;
use App\Models\TripComment;
use App\Models\User;
use Src\Social\Application\UseCases\AddCommentUseCase;
use Src\Social\Domain\Repositories\CommentRepository;
use Tests\TestCase;

/**
 * @internal
 */
class AddCommentUseCaseTest extends TestCase
{
    public function test_invoke_delegates_to_repository_and_returns_comment(): void
    {
        // Arrange
        $expectedComment = [
            'id'      => 1,
            'trip_id' => Trip::factory()->create([
                'id' => 'trip-abc',
            ]),
            'user_id' => User::factory()->create()->id,
            'text'    => 'Great trip!',
        ];

        $repository = $this->createMock(CommentRepository::class);
        $repository->expects($this->once())
            ->method('create')
            ->with('trip-abc', $expectedComment['user_id'], 'Great trip!')
            ->willReturn(TripComment::factory()->create($expectedComment));

        $useCase = new AddCommentUseCase($repository);

        // Act
        $result = $useCase('trip-abc', $expectedComment['user_id'], 'Great trip!');

        // Assert
        $this->assertEquals($expectedComment['id'], $result->id);
    }
}
