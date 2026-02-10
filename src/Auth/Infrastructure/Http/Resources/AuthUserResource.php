<?php

namespace Src\Auth\Infrastructure\Http\Resources;

use Src\Auth\Application\DTO\Out\AuthUserDTO;

readonly class AuthUserResource implements \JsonSerializable
{
    public function __construct(
        private AuthUserDTO $authUser,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'user' => [
                'name'     => $this->authUser->user->name,
                'email'    => $this->authUser->user->email,
                'username' => $this->authUser->user->username,
                'avatar'   => $this->authUser->user->avatar->url,
            ],
            'token' => $this->authUser->authToken->value,
        ];
    }
}
