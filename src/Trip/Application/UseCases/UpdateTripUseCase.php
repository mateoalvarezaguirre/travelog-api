<?php

declare(strict_types=1);

namespace Src\Trip\Application\UseCases;

use Src\Trip\Application\DTOs\In\UpdateTripDTO;
use Src\Trip\Domain\Entities\TripEntity;
use Src\Trip\Domain\Enums\StatusEnum;
use Src\Trip\Domain\Enums\VisibilityEnum;
use Src\Trip\Domain\Exceptions\TripNotFoundException;
use Src\Trip\Domain\Exceptions\UnauthorizedTripActionException;
use Src\Trip\Domain\Repositories\TripRepository;
use Src\Trip\Domain\Services\ExcerptGenerator;

readonly class UpdateTripUseCase
{
    public function __construct(
        private TripRepository $repository,
    ) {}

    public function __invoke(UpdateTripDTO $dto): TripEntity
    {
        $trip = $this->repository->findById($dto->tripId);

        if ($trip === null) {
            throw new TripNotFoundException();
        }

        if ($trip->owner->id !== $dto->ownerId) {
            throw new UnauthorizedTripActionException();
        }

        $data = array_filter([
            'title'      => $dto->title,
            'content'    => $dto->content,
            'date'       => $dto->date,
            'location'   => $dto->location,
            'latitude'   => $dto->latitude,
            'longitude'  => $dto->longitude,
            'status'     => $dto->status   !== null ? StatusEnum::from($dto->status) : null,
            'visibility' => $dto->isPublic !== null
                ? ($dto->isPublic ? VisibilityEnum::PUBLIC : VisibilityEnum::PRIVATE)
                : null,
        ], fn ($value) => $value !== null);

        if ($dto->content !== null) {
            $data['excerpt'] = ExcerptGenerator::generate($dto->content);
        }

        if (isset($data['status']) && $data['status'] === StatusEnum::PUBLISHED && $trip->publishedAt === null) {
            $data['published_at'] = now();
        }

        $this->repository->update($dto->tripId, $data);

        if ($dto->tags !== null) {
            $this->repository->syncTagsByTripId($dto->tripId, $dto->tags);
        }

        if ($dto->imageIds !== null) {
            $this->repository->syncMediaByTripId($dto->tripId, $dto->imageIds);
        }

        $updatedTrip = $this->repository->findById($dto->tripId);
        assert($updatedTrip !== null);

        return $updatedTrip;
    }
}
