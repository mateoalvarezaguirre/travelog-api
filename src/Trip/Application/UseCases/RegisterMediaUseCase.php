<?php

declare(strict_types=1);

namespace Src\Trip\Application\UseCases;

use Src\Trip\Application\DTOs\In\RegisterMediaDTO;
use Src\Trip\Domain\Repositories\TripMediaRepository;
use Src\Trip\Domain\ValueObjects\RegisteredMedia;

readonly class RegisterMediaUseCase
{
    public function __construct(
        private TripMediaRepository $mediaRepository,
    ) {}

    public function __invoke(RegisterMediaDTO $dto): RegisteredMedia
    {
        return $this->mediaRepository->register(
            $dto->uploadedBy,
            $dto->url,
            $dto->caption,
        );
    }
}
