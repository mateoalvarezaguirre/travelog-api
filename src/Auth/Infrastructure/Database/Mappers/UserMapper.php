<?php

declare(strict_types=1);

namespace Src\Auth\Infrastructure\Database\Mappers;

use App\Models\User;
use Src\Auth\Domain\Entities\UserEntity;
use Src\Auth\Domain\Objects\Password;
use Src\Auth\Domain\ValueObjects\Avatar;
use Src\Auth\Domain\ValueObjects\GoogleId;
use Src\Shared\Core\Domain\ValueObjects\Email;

class UserMapper
{
    public static function fromModelToEntity(User $userModel): UserEntity
    {
        $userEntity = new UserEntity(
            $userModel->name,
            new Email($userModel->email),
            new Password($userModel->password),
            $userModel->username,
            $userModel->google_id ? new GoogleId($userModel->google_id) : null,
            $userModel->avatar ? new Avatar($userModel->avatar) : null,
            $userModel->email_verified_at !== null
        );

        $userEntity->setId($userModel->id);

        return $userEntity;
    }
}
