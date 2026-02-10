<?php

declare(strict_types=1);

namespace Src\Trip\Domain\ValueObjects;

class Owner
{
    public function __construct(
        public readonly int $id,
        private ?string $name = null,
        private ?string $profilePicture = null,
        private ?string $phrase = null,
        private ?string $username = null,
    ) {}

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): void
    {
        $this->profilePicture = $profilePicture;
    }

    public function getPhrase(): ?string
    {
        return $this->phrase;
    }

    public function setPhrase(string $phrase): void
    {
        $this->phrase = $phrase;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }
}
