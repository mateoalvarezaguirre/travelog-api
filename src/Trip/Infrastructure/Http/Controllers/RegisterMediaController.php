<?php

declare(strict_types=1);

namespace Src\Trip\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Src\Trip\Application\DTOs\In\RegisterMediaDTO;
use Src\Trip\Application\UseCases\RegisterMediaUseCase;
use Src\Trip\Infrastructure\Http\Requests\RegisterMediaRequest;

readonly class RegisterMediaController
{
    public function __construct(
        private RegisterMediaUseCase $useCase,
    ) {}

    public function __invoke(RegisterMediaRequest $request): JsonResponse
    {
        $dto = new RegisterMediaDTO(
            uploadedBy: $request->user()->id,
            url: $request->input('url'),
            caption: $request->input('caption'),
        );

        $media = ($this->useCase)($dto);

        return response()->json([
            'id'  => $media->id,
            'url' => $media->url,
        ], 201);
    }
}
