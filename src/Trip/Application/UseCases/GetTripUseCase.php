<?php

declare(strict_types=1);

namespace Src\Trip\Application\UseCases;

use Src\Trip\Application\DTOs\Out\GetTripResult;
use Src\Trip\Domain\Exceptions\TripNotFoundException;
use Src\Trip\Domain\Repositories\TripRepository;

readonly class GetTripUseCase
{
    public function __construct(
        private TripRepository $repository,
    ) {}

    public function __invoke(string $tripId, ?int $authUserId = null): GetTripResult
    {
        $trip = $this->repository->findById($tripId);

        if ($trip === null) {
            throw new TripNotFoundException();
        }

        $isLiked = false;
        if ($authUserId !== null) {
            $trip->setIsLiked($this->repository->isLikedByUser($tripId, $authUserId));
            $isLiked = $trip->isLiked();
        }

        return new GetTripResult($trip, $isLiked);
    }
}
