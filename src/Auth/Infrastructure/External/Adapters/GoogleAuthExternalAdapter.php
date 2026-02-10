<?php

declare(strict_types=1);

namespace Src\Auth\Infrastructure\External\Adapters;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Src\Auth\Domain\Contracts\GoogleManagement;
use Src\Auth\Domain\Entities\UserEntity;
use Src\Auth\Domain\Exceptions\MissingRequiredFieldsFromAuthPartner;
use Src\Auth\Domain\Objects\Password;
use Src\Auth\Domain\ValueObjects\Avatar;
use Src\Auth\Domain\ValueObjects\GoogleId;
use Src\Shared\Core\Domain\Exceptions\ExternalCommunicationException;
use Src\Shared\Core\Domain\ValueObjects\Email;

class GoogleAuthExternalAdapter implements GoogleManagement
{
    /**
     * @throws ExternalCommunicationException
     * @throws ConnectionException
     * @throws MissingRequiredFieldsFromAuthPartner
     */
    public function getUserByGoogleId(GoogleId $googleId): UserEntity
    {
        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $googleId->value,
        ]);

        if (! $response->ok()) {
            throw new ExternalCommunicationException();
        }

        $payload  = $response->json();
        $googleId = $payload['sub']     ?? null;
        $email    = $payload['email']   ?? null;
        $name     = $payload['name']    ?? 'Google User';
        $userName = $payload['email']   ?? Str::slug($name) . '-' . Str::random(5);
        $avatar   = $payload['picture'] ?? null;

        if (! $googleId || ! $email) {
            throw new MissingRequiredFieldsFromAuthPartner();
        }

        return new UserEntity(
            name: $name,
            email: new Email($email),
            password: new Password(Str::random(32)),
            username: $userName,
            googleId: new GoogleId($googleId),
            avatar: new Avatar($avatar),
        );
    }
}
