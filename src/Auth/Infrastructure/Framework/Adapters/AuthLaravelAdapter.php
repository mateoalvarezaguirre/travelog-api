<?php

declare(strict_types=1);

namespace Src\Auth\Infrastructure\Framework\Adapters;

use App\Models\User;
use Src\Auth\Domain\Contracts\AuthManagement;
use Src\Auth\Domain\Entities\UserEntity;
use Src\Auth\Domain\Exceptions\UnauthenticatedUserException;
use Src\Auth\Domain\ValueObjects\AuthToken;

class AuthLaravelAdapter implements AuthManagement
{
    /**
     * @throws UnauthenticatedUserException
     */
    public function getAuthToken(UserEntity $user): AuthToken
    {
        $userModel = User::find($user->getId());

        if (! $userModel) {
            throw new UnauthenticatedUserException();
        }

        return new AuthToken($userModel->createToken('auth_token')->plainTextToken);
    }
}
