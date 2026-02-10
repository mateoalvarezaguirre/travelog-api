<?php

declare(strict_types=1);

namespace Src\Profile\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Profile\Application\UseCases\GetProfileByUsernameUseCase;
use Src\Profile\Domain\Exceptions\UserNotFoundExceptions;

readonly class GetUserByUsernameController
{
    public function __construct(
        private GetProfileByUsernameUseCase $useCase,
    ) {}

    public function __invoke(Request $request, string $username): JsonResponse
    {
        try {
            $profile = ($this->useCase)($username, $request->user()->id);
        } catch (UserNotFoundExceptions) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($profile->toArray());
    }
}
