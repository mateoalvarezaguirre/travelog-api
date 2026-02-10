<?php

declare(strict_types=1);

namespace Src\Profile\Infrastructure\Database\Repositories;

use Src\Profile\Domain\Repositories\UserRepository;
use Src\Profile\Domain\ValueObjects\User;
use Src\Shared\Core\Domain\Exceptions\DatabaseException;
use Src\Shared\Core\Domain\Util\DbKickable;
use Src\Shared\Core\Domain\ValueObjects\Email;

class UserEloquentRepository implements UserRepository
{
    use DbKickable;

    /**
     * @throws DatabaseException
     */
    public function findByEmail(Email $email): ?User
    {
        return $this->kick(function () use ($email): ?User {
            $userModel = \App\Models\User::where('email', $email->value)
                ->select([
                    'id',
                    'name',
                    'email',
                    'username',
                    'avatar',
                ])
                ->first();

            if (! $userModel) {
                return null;
            }

            return new User(
                $userModel->id,
                $userModel->name,
                $userModel->email,
                $userModel->username,
                $userModel->avatar ?? ''
            );
        });
    }
}
