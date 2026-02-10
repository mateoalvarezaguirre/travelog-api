<?php

namespace Src\Profile\Infrastructure\Http\Controllers;

use Src\Profile\Application\UseCases\GetProfileUseCase;
use Src\Profile\Domain\Exceptions\UserNotFoundExceptions;
use Src\Profile\Domain\Repositories\UserRepository;
use Src\Profile\Infrastructure\Http\Requests\GetProfileRequest;
use Src\Profile\Infrastructure\Http\Resources\ProfileResource;

readonly class GetProfileController
{
    public function __construct(
        private UserRepository $repository
    ) {}

    /**
     * @throws UserNotFoundExceptions
     */
    public function __invoke(GetProfileRequest $request): ProfileResource
    {
        $useCase = new GetProfileUseCase(
            $request->dto(),
            $this->repository
        );

        return new ProfileResource($useCase());
    }
}
