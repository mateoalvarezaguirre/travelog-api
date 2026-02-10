<?php

declare(strict_types=1);

namespace Src\Auth\Infrastructure\Database\Repositories;

use App\Models\User;
use Src\Auth\Domain\Entities\UserEntity;
use Src\Auth\Domain\Repositories\UserRepository;
use Src\Auth\Infrastructure\Database\Mappers\UserMapper;
use Src\Shared\Core\Domain\Exceptions\DatabaseException;
use Src\Shared\Core\Domain\Util\DbKickable;
use Src\Shared\Core\Domain\ValueObjects\Email;

class UserEloquentRepository implements UserRepository
{
    use DbKickable;

    /**
     * @throws DatabaseException
     */
    public function save(UserEntity $user): void
    {
        $this->kick(function () use ($user): void {
            $userModelId = $user->getId();

            $userModel            = $userModelId ? User::findOrFail($userModelId) : new User();
            $userModel->name      = $user->name;
            $userModel->email     = $user->email->value;
            $userModel->password  = $user->password->getHash();
            $userModel->username  = $user->username;
            $userModel->avatar    = $user->avatar?->url;
            $userModel->google_id = $user->googleId?->value;
            $userModel->save();

            if (! $userModelId) {
                $user->setId($userModel->id);
            }
        });
    }

    /**
     * @throws DatabaseException
     */
    public function saveByEmail(UserEntity $user): void
    {
        $this->kick(function () use ($user): void {
            $userModel = User::where('email', $user->email->value)->first();

            if (null === $userModel) {
                $this->save($user);

                return;
            }

            $user->setId($userModel->id);
            $this->save($user);
        });
    }

    /**
     * @throws DatabaseException
     */
    public function findByEmail(Email $email): ?UserEntity
    {
        return $this->kick(function () use ($email): ?UserEntity {
            $userModel = User::where('email', $email->value)->first();

            if (null === $userModel) {
                return null;
            }

            return UserMapper::fromModelToEntity($userModel);
        });
    }
}
