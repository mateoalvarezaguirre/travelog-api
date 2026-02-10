<?php

declare(strict_types=1);

namespace Src\Trip\Application\DTOs\In;

readonly class ListTripsDTO
{
    public function __construct(
        public int $ownerId,
        public int $page = 1,
        public ?string $search = null,
        public ?string $tag = null,
        public ?string $status = null,
        public string $tab = 'recent',
    ) {}
}
