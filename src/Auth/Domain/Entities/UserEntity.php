<?php

declare(strict_types=1);

namespace Src\Auth\Domain\Entities;

use Src\Auth\Domain\Objects\Password;
use Src\Auth\Domain\ValueObjects\Avatar;
use Src\Auth\Domain\ValueObjects\GoogleId;
use Src\Shared\Core\Domain\ValueObjects\Email;

class UserEntity
{
    private int $id = 0;

    public function __construct(
        public readonly string $name,
        public readonly Email $email,
        public readonly Password $password,
        public readonly string $username,
        public readonly ?GoogleId $googleId = null,
        public readonly ?Avatar $avatar = null,
        public readonly bool $emailVerified = false,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
