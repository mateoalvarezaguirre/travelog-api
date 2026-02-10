<?php

declare(strict_types=1);

namespace Src\Trip\Domain\ValueObjects;

readonly class TripFilters
{
    public function __construct(
        public int $page = 1,
        public ?string $search = null,
        public ?string $tag = null,
        public ?string $status = null,
        public string $tab = 'recent',
    ) {}
}
