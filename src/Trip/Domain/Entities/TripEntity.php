<?php

declare(strict_types=1);

namespace Src\Trip\Domain\Entities;

use Carbon\Carbon;
use Src\Trip\Domain\Enums\StatusEnum;
use Src\Trip\Domain\Enums\VisibilityEnum;
use Src\Trip\Domain\ValueObjects\Engagement;
use Src\Trip\Domain\ValueObjects\Owner;

class TripEntity
{
    private string $uuid;

    private ?string $excerpt = null;

    private ?Carbon $date = null;

    private ?string $location = null;

    private ?float $latitude = null;

    private ?float $longitude = null;

    /** @var string[] */
    private array $tags = [];

    /** @var int[] */
    private array $imageIds = [];

    /** @var array<int, array<string, mixed>> */
    private array $images = [];

    private bool $isLiked = false;

    private ?Carbon $updatedAt = null;

    public function __construct(
        public readonly string $title,
        public readonly string $content,
        public readonly Owner $owner,
        public readonly StatusEnum $status,
        public readonly VisibilityEnum $visibility,
        public readonly Engagement $engagement,
        public readonly Carbon $createdAt,
        public readonly ?Carbon $publishedAt = null,
        public readonly ?string $privateContent = null,
    ) {}

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function setId(string $id): void
    {
        $this->uuid = $id;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(?string $excerpt): void
    {
        $this->excerpt = $excerpt;
    }

    public function getDate(): ?Carbon
    {
        return $this->date;
    }

    public function setDate(?Carbon $date): void
    {
        $this->date = $date;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param string[] $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return int[]
     */
    public function getImageIds(): array
    {
        return $this->imageIds;
    }

    /**
     * @param int[] $imageIds
     */
    public function setImageIds(array $imageIds): void
    {
        $this->imageIds = $imageIds;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param array<int, array<string, mixed>> $images
     */
    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    public function isPublic(): bool
    {
        return $this->visibility === VisibilityEnum::PUBLIC;
    }

    public function isLiked(): bool
    {
        return $this->isLiked;
    }

    public function setIsLiked(bool $isLiked): void
    {
        $this->isLiked = $isLiked;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?Carbon $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
