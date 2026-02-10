<?php

declare(strict_types=1);

namespace Src\Place\Application\UseCases;

use Src\Place\Domain\Repositories\PlaceRepository;

readonly class ListPlacesUseCase
{
    public function __construct(
        private PlaceRepository $placeRepository,
    ) {}

    /**
     * @return mixed[]
     */
    public function __invoke(int $userId): array
    {
        return $this->placeRepository->listByUserId($userId);
    }
}
