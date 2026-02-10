<?php

declare(strict_types=1);

namespace Src\Trip\Application\DTOs\In;

readonly class CreateTripDTO
{
    /**
     * @param string[] $tags
     * @param int[] $imageIds
     */
    public function __construct(
        public string $title,
        public string $content,
        public int $ownerId,
        public ?string $date = null,
        public ?string $location = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public array $tags = [],
        public string $status = 'draft',
        public bool $isPublic = true,
        public array $imageIds = [],
    ) {}
}
