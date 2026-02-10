<?php

declare(strict_types=1);

namespace Src\Profile\Domain\Repositories;

use Src\Profile\Domain\ValueObjects\ProfileView;
use Src\Profile\Domain\ValueObjects\StatsView;
use Src\Profile\Domain\ValueObjects\UpdateProfileData;

interface ProfileRepository
{
    public function findUserIdByUsername(string $username): ?int;

    public function getProfileView(int $userId, ?int $authUserId): ?ProfileView;

    public function getStatsView(int $userId): ?StatsView;

    public function updateProfile(int $userId, UpdateProfileData $data): ProfileView;
}
