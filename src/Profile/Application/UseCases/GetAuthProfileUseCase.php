<?php

declare(strict_types=1);

namespace Src\Profile\Application\UseCases;

use Src\Profile\Domain\Exceptions\UserNotFoundExceptions;
use Src\Profile\Domain\Repositories\ProfileRepository;
use Src\Profile\Domain\ValueObjects\ProfileView;

readonly class GetAuthProfileUseCase
{
    public function __construct(
        private ProfileRepository $profileRepository,
    ) {}

    /**
     * @throws UserNotFoundExceptions
     */
    public function __invoke(int $userId): ProfileView
    {
        $profile = $this->profileRepository->getProfileView($userId, $userId);

        if ($profile === null) {
            throw new UserNotFoundExceptions();
        }

        return $profile;
    }
}
