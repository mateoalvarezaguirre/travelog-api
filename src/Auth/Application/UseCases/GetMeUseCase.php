<?php

declare(strict_types=1);

namespace Src\Auth\Application\UseCases;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

readonly class GetMeUseCase
{
    /**
     * @return array<string, mixed>
     */
    public function __invoke(?Authenticatable $user): array
    {
        assert($user instanceof User);

        return [
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'username' => $user->username,
            'avatar'   => $user->avatar,
        ];
    }
}
