<?php

declare(strict_types=1);

namespace Src\Place\Application\UseCases;

use Src\Place\Domain\Exceptions\PlaceNotFoundException;
use Src\Place\Domain\Exceptions\UnauthorizedPlaceActionException;
use Src\Place\Domain\Repositories\PlaceRepository;

readonly class DeletePlaceUseCase
{
    public function __construct(
        private PlaceRepository $placeRepository,
    ) {}

    /**
     * @throws PlaceNotFoundException
     * @throws UnauthorizedPlaceActionException
     */
    public function __invoke(int $placeId, int $userId): void
    {
        $this->placeRepository->delete($placeId, $userId);
    }
}
