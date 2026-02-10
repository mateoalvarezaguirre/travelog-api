<?php

namespace Src\Profile\Domain\Repositories;

use Src\Profile\Domain\ValueObjects\User;
use Src\Shared\Core\Domain\ValueObjects\Email;

interface UserRepository
{
    public function findByEmail(Email $email): ?User;
}
