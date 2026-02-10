<?php

declare(strict_types=1);

namespace Src\Profile\Domain\ValueObjects;

readonly class ProfileView
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $username,
        public ?string $bio,
        public ?string $avatar,
        public ?string $coverPhoto,
        public ?string $location,
        public int $journalCount,
        public int $followersCount,
        public int $followingCount,
        public int $countriesVisited,
        public bool $isFollowing,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'email'            => $this->email,
            'username'         => $this->username,
            'bio'              => $this->bio,
            'avatar'           => $this->avatar,
            'coverPhoto'       => $this->coverPhoto,
            'location'         => $this->location,
            'journalCount'     => $this->journalCount,
            'followersCount'   => $this->followersCount,
            'followingCount'   => $this->followingCount,
            'countriesVisited' => $this->countriesVisited,
            'isFollowing'      => $this->isFollowing,
        ];
    }
}
