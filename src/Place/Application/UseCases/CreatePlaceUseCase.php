<?php

declare(strict_types=1);

namespace Src\Place\Application\UseCases;

use App\Models\Place;
use Src\Place\Application\DTOs\CreatePlaceDTO;
use Src\Place\Domain\Repositories\PlaceRepository;

readonly class CreatePlaceUseCase
{
    public function __construct(
        private PlaceRepository $placeRepository,
    ) {}

    public function __invoke(CreatePlaceDTO $dto): Place
    {
        return $this->placeRepository->create(
            $dto->userId,
            $dto->name,
            $dto->country,
            $dto->date,
            $dto->latitude,
            $dto->longitude,
            $dto->markerType,
            $dto->image,
        );
    }
}
