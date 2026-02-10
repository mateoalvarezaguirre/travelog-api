<?php

namespace Src\Profile\Application\UseCases;

use Src\Profile\Application\DTO\In\GetProfileDTO;
use Src\Profile\Application\DTO\Out\UserDTO;
use Src\Profile\Domain\Exceptions\UserNotFoundExceptions;
use Src\Profile\Domain\Repositories\UserRepository;
use Src\Shared\Core\Domain\ValueObjects\Email;

readonly class GetProfileUseCase
{
    public function __construct(
        private GetProfileDTO $dto,
        private UserRepository $repository
    ) {}

    /**
     * @throws UserNotFoundExceptions
     */
    public function __invoke(): UserDTO
    {
        $user = $this->repository->findByEmail(
            new Email($this->dto->email)
        );

        if (! $user) {
            throw new UserNotFoundExceptions();
        }

        return new UserDTO($user);
    }
}
