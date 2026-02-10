<?php

declare(strict_types=1);

namespace Src\Profile\Application\UseCases;

use Src\Profile\Domain\Exceptions\UserNotFoundExceptions;
use Src\Profile\Domain\Repositories\ProfileRepository;
use Src\Profile\Domain\ValueObjects\StatsView;

readonly class GetUserStatsUseCase
{
    public function __construct(
        private ProfileRepository $profileRepository,
    ) {}

    /**
     * @throws UserNotFoundExceptions
     */
    public function __invoke(string $username): StatsView
    {
        $userId = $this->profileRepository->findUserIdByUsername($username);

        if ($userId === null) {
            throw new UserNotFoundExceptions();
        }

        $stats = $this->profileRepository->getStatsView($userId);
        if ($stats === null) {
            throw new UserNotFoundExceptions();
        }

        return $stats;
    }
}
