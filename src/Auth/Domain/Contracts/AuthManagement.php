<?php

namespace Src\Auth\Domain\Contracts;

use Src\Auth\Domain\Entities\UserEntity;
use Src\Auth\Domain\ValueObjects\AuthToken;
use Src\Auth\Domain\ValueObjects\Credentials;

interface AuthManagement
{
    public function attempt(Credentials $credentials): bool;

    public function getAuthUser(): UserEntity;

    public function getAuthToken(): AuthToken;
}
