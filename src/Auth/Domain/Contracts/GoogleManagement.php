<?php

namespace Src\Auth\Domain\Contracts;

use Src\Auth\Domain\Entities\UserEntity;
use Src\Auth\Domain\ValueObjects\GoogleId;

interface GoogleManagement
{
    public function getUserByGoogleId(GoogleId $googleId): UserEntity;
}
