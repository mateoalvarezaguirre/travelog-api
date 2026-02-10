<?php

declare(strict_types=1);

namespace Src\Auth\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Sanctum\PersonalAccessToken;

readonly class LogoutController
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        if ($user === null) {
            return response()->noContent();
        }

        /** @var null|PersonalAccessToken $token */
        $token = $user->currentAccessToken();
        $token?->delete();

        return response()->noContent();
    }
}
