<?php

declare(strict_types=1);

namespace Tests\Unit\Profile;

use Src\Profile\Application\UseCases\GetAuthProfileUseCase;
use Src\Profile\Domain\Exceptions\UserNotFoundExceptions;
use Src\Profile\Domain\Repositories\ProfileRepository;
use Src\Profile\Domain\ValueObjects\ProfileView;
use Tests\TestCase;

/**
 * @internal
 */
class GetAuthProfileUseCaseTest extends TestCase
{
    public function test_invoke_throws_when_profile_not_found(): void
    {
        $repository = $this->createMock(ProfileRepository::class);
        $repository->method('getProfileView')->with(1, 1)->willReturn(null);

        $useCase = new GetAuthProfileUseCase($repository);

        $this->expectException(UserNotFoundExceptions::class);

        $useCase(1);
    }

    public function test_invoke_returns_profile_view_when_found(): void
    {
        $profile = new ProfileView(
            id: 1,
            name: 'John',
            email: 'john@example.com',
            username: 'john',
            bio: null,
            avatar: null,
            coverPhoto: null,
            location: null,
            journalCount: 0,
            followersCount: 0,
            followingCount: 0,
            countriesVisited: 0,
            isFollowing: false,
        );
        $repository = $this->createMock(ProfileRepository::class);
        $repository->method('getProfileView')->with(1, 1)->willReturn($profile);

        $useCase = new GetAuthProfileUseCase($repository);

        $result = $useCase(1);

        $this->assertSame($profile, $result);
        $this->assertSame('john', $result->username);
    }
}
