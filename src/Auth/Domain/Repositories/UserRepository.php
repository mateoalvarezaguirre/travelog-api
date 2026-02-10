<?php

declare(strict_types=1);

namespace Src\Auth\Domain\Repositories;

use Src\Auth\Domain\Entities\UserEntity;
use Src\Shared\Core\Domain\ValueObjects\Email;

interface UserRepository
{
    public function save(UserEntity $user): void;

    public function saveByEmail(UserEntity $user): void;

    public function findByEmail(Email $email): ?UserEntity;
}
