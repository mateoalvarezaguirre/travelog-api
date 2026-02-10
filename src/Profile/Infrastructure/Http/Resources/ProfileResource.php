<?php

declare(strict_types=1);

namespace Src\Profile\Infrastructure\Http\Resources;

use Src\Profile\Domain\ValueObjects\User;

readonly class ProfileResource implements \JsonSerializable
{
    public function __construct(
        private User $user,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'user' => [
                'name'     => $this->user->name,
                'email'    => $this->user->email,
                'username' => $this->user->username,
                'avatar'   => $this->user->avatarUrl,
            ],
        ];
    }
}
