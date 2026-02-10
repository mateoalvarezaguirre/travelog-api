<?php

declare(strict_types=1);

namespace Src\Auth\Application\UseCases;

use Illuminate\Support\Facades\Password;

readonly class ForgotPasswordUseCase
{
    /**
     * @return array{status: string, message: string}
     */
    public function __invoke(string $email): array
    {
        $status = Password::sendResetLink(['email' => $email]);

        return [
            'status'  => $status,
            'message' => __($status),
        ];
    }
}
