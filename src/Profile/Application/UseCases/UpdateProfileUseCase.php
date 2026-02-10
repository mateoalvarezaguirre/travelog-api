<?php

declare(strict_types=1);

namespace Src\Profile\Application\UseCases;

use Src\Profile\Domain\Repositories\ProfileRepository;
use Src\Profile\Domain\ValueObjects\ProfileView;
use Src\Profile\Domain\ValueObjects\UpdateProfileData;

readonly class UpdateProfileUseCase
{
    public function __construct(
        private ProfileRepository $profileRepository,
    ) {}

    public function __invoke(int $userId, UpdateProfileData $data): ProfileView
    {
        return $this->profileRepository->updateProfile($userId, $data);
    }
}
