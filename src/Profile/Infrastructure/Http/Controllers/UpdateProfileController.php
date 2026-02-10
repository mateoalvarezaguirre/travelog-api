<?php

declare(strict_types=1);

namespace Src\Profile\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Src\Profile\Application\UseCases\UpdateProfileUseCase;
use Src\Profile\Domain\ValueObjects\UpdateProfileData;
use Src\Profile\Infrastructure\Http\Requests\UpdateProfileRequest;

readonly class UpdateProfileController
{
    public function __construct(
        private UpdateProfileUseCase $useCase,
    ) {}

    public function __invoke(UpdateProfileRequest $request): JsonResponse
    {
        $data = new UpdateProfileData(
            name: $request->input('name'),
            location: $request->input('location'),
            avatar: $request->input('avatar'),
            coverPhoto: $request->input('coverPhoto'),
            bio: $request->has('bio') ? $request->input('bio') : null,
        );

        $profile = ($this->useCase)($request->user()->id, $data);

        return response()->json($profile->toArray());
    }
}
