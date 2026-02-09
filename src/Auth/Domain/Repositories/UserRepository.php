<?php

namespace Src\Auth\Domain\Repositories;

use Src\Auth\Domain\Entities\UserEntity;
use Src\Auth\Domain\ValueObjects\GoogleId;

interface UserRepository
{
    public function create(UserEntity $user): void;

    public function findByGoogleId(GoogleId $googleId): ?UserEntity;

    public function findByEmail(string $email): ?UserEntity;
}
