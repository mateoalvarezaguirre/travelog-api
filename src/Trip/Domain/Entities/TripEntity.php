<?php

namespace Src\Trip\Domain\Entities;

use Carbon\Carbon;
use Src\Trip\Domain\Enums\StatusEnum;
use Src\Trip\Domain\Enums\VisibilityEnum;
use Src\Trip\Domain\ValueObjects\Engagement;
use Src\Trip\Domain\ValueObjects\Owner;

class TripEntity
{
    private string $uuid;
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
}
