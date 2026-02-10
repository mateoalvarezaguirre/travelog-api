<?php

declare(strict_types=1);

namespace Src\Auth\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Src\Auth\Application\UseCases\ForgotPasswordUseCase;
use Src\Auth\Infrastructure\Http\Requests\ForgotPasswordRequest;

readonly class ForgotPasswordController
{
    public function __construct(
        private ForgotPasswordUseCase $forgotPasswordUseCase,
    ) {}

    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        $result = ($this->forgotPasswordUseCase)($request->input('email'));

        if ($result['status'] !== Password::RESET_LINK_SENT) {
            return response()->json(['message' => $result['message']], 422);
        }

        return response()->json(['message' => $result['message']]);
    }
}
