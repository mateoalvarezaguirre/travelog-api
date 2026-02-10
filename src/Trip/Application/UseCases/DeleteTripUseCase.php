<?php

declare(strict_types=1);

namespace Src\Trip\Application\UseCases;

use Src\Trip\Domain\Exceptions\TripNotFoundException;
use Src\Trip\Domain\Exceptions\UnauthorizedTripActionException;
use Src\Trip\Domain\Repositories\TripRepository;

readonly class DeleteTripUseCase
{
    public function __construct(
        private TripRepository $repository,
    ) {}

    public function __invoke(string $tripId, int $ownerId): void
    {
        $trip = $this->repository->findById($tripId);

        if ($trip === null) {
            throw new TripNotFoundException();
        }

        if ($trip->owner->id !== $ownerId) {
            throw new UnauthorizedTripActionException();
        }

        $this->repository->delete($tripId);
    }
}
