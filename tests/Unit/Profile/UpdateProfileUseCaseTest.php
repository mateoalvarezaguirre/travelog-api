<?php

declare(strict_types=1);

namespace Tests\Unit\Profile;

use Src\Profile\Application\UseCases\UpdateProfileUseCase;
use Src\Profile\Domain\Repositories\ProfileRepository;
use Src\Profile\Domain\ValueObjects\ProfileView;
use Src\Profile\Domain\ValueObjects\UpdateProfileData;
use Tests\TestCase;

/**
 * @internal
 */
class UpdateProfileUseCaseTest extends TestCase
{
    public function test_invoke_delegates_to_repository_and_returns_profile_view(): void
    {
        // Arrange
        $data = new UpdateProfileData(
            name: 'John Doe',
            location: 'Barcelona',
            bio: 'Traveler',
        );

        $expectedProfile = new ProfileView(
            id: 5,
            name: 'John Doe',
            email: 'john@example.com',
            username: 'johndoe',
            bio: 'Traveler',
            avatar: null,
            coverPhoto: null,
            location: 'Barcelona',
            journalCount: 3,
            followersCount: 10,
            followingCount: 5,
            countriesVisited: 8,
            isFollowing: false,
        );

        $repository = $this->createMock(ProfileRepository::class);
        $repository->expects($this->once())
            ->method('updateProfile')
            ->with(5, $data)
            ->willReturn($expectedProfile);

        $useCase = new UpdateProfileUseCase($repository);

        // Act
        $result = $useCase(5, $data);

        // Assert
        $this->assertInstanceOf(ProfileView::class, $result);
        $this->assertSame(5, $result->id);
        $this->assertSame('John Doe', $result->name);
        $this->assertSame('Barcelona', $result->location);
        $this->assertSame('Traveler', $result->bio);
    }
}
