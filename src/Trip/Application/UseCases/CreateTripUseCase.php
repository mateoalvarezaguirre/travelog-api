<?php

declare(strict_types=1);

namespace Src\Trip\Application\UseCases;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Src\Trip\Application\DTOs\In\CreateTripDTO;
use Src\Trip\Domain\Entities\TripEntity;
use Src\Trip\Domain\Enums\StatusEnum;
use Src\Trip\Domain\Enums\VisibilityEnum;
use Src\Trip\Domain\Repositories\TripRepository;
use Src\Trip\Domain\Services\ExcerptGenerator;
use Src\Trip\Domain\ValueObjects\Engagement;
use Src\Trip\Domain\ValueObjects\Owner;

readonly class CreateTripUseCase
{
    public function __construct(
        private TripRepository $repository,
    ) {}

    public function __invoke(CreateTripDTO $dto): TripEntity
    {
        $status     = StatusEnum::from($dto->status);
        $visibility = $dto->isPublic ? VisibilityEnum::PUBLIC : VisibilityEnum::PRIVATE;

        $owner = new Owner($dto->ownerId);

        $trip = new TripEntity(
            title: $dto->title,
            content: $dto->content,
            owner: $owner,
            status: $status,
            visibility: $visibility,
            engagement: new Engagement(),
            createdAt: now(),
            publishedAt: $status === StatusEnum::PUBLISHED ? now() : null,
        );

        $trip->setId(Str::uuid()->toString());
        $trip->setExcerpt(ExcerptGenerator::generate($dto->content));
        $trip->setDate($dto->date ? Carbon::parse($dto->date) : null);
        $trip->setLocation($dto->location);
        $trip->setLatitude($dto->latitude);
        $trip->setLongitude($dto->longitude);
        $trip->setTags($dto->tags);
        $trip->setImageIds($dto->imageIds);

        return $this->repository->save($trip);
    }
}
