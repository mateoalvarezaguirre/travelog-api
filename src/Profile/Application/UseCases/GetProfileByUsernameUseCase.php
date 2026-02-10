<?php

declare(strict_types=1);

namespace Src\Profile\Application\UseCases;

use Src\Profile\Domain\Exceptions\UserNotFoundExceptions;
use Src\Profile\Domain\Repositories\ProfileRepository;
use Src\Profile\Domain\ValueObjects\ProfileView;

readonly class GetProfileByUsernameUseCase
{
    public function __construct(
        private ProfileRepository $profileRepository,
    ) {}

    /**
     * @throws UserNotFoundExceptions
     */
    public function __invoke(string $username, ?int $authUserId): ProfileView
    {
        $userId = $this->profileRepository->findUserIdByUsername($username);

        if ($userId === null) {
            throw new UserNotFoundExceptions();
        }

        $profile = $this->profileRepository->getProfileView($userId, $authUserId);
        if ($profile === null) {
            throw new UserNotFoundExceptions();
        }

        return $profile;
    }
}
