<?php

namespace Src\Auth\Infrastructure\Framework\Adapters;

use Illuminate\Support\Facades\Auth;
use Src\Auth\Domain\Contracts\AuthManagement;
use Src\Auth\Domain\Entities\UserEntity;
use Src\Auth\Domain\Exceptions\UnauthenticatedUserException;
use Src\Auth\Domain\ValueObjects\AuthToken;
use Src\Auth\Domain\ValueObjects\Credentials;
use Src\Auth\Infrastructure\Framework\Mappers\UserMapper;

class AuthLaravelAdapter implements AuthManagement
{

    public function attempt(Credentials $credentials): bool
    {
        return Auth::attempt([
            'email' => $credentials->email,
            'password' => $credentials->password
        ]);
    }

    /**
     * @throws UnauthenticatedUserException
     */
    public function getAuthUser(): UserEntity
    {
        $user = Auth::user();

        if (! $user) {
            throw new UnauthenticatedUserException();
        }

        return UserMapper::fromModelToEntity($user);
    }

    /**
     * @throws UnauthenticatedUserException
     */
    public function getAuthToken(): AuthToken
    {
        $user = Auth::user();

        if (! $user) {
            throw new UnauthenticatedUserException();
        }

        return new AuthToken($user->createToken('auth_token')->plainTextToken);
    }
}
