<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Http\Resources;

use App\Models\User;

readonly class UserSummaryResource implements \JsonSerializable
{
    public function __construct(
        private User $user,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id'       => $this->user->id,
            'name'     => $this->user->name,
            'username' => $this->user->username,
            'avatar'   => $this->user->avatar,
        ];
    }

    /**
     * @param User[] $users
     *
     * @return array<int, array<string, mixed>>
     */
    public static function collection(array $users): array
    {
        return array_map(
            fn (User $user) => (new self($user))->jsonSerialize(),
            $users
        );
    }
}
