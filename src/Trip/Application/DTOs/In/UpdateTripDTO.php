<?php

declare(strict_types=1);

namespace Src\Trip\Application\DTOs\In;

readonly class UpdateTripDTO
{
    /**
     * @param null|string[] $tags
     * @param null|int[] $imageIds
     */
    public function __construct(
        public string $tripId,
        public int $ownerId,
        public ?string $title = null,
        public ?string $content = null,
        public ?string $date = null,
        public ?string $location = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?array $tags = null,
        public ?string $status = null,
        public ?bool $isPublic = null,
        public ?array $imageIds = null,
    ) {}
}
