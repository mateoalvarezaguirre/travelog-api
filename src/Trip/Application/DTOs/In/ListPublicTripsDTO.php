<?php

declare(strict_types=1);

namespace Src\Trip\Application\DTOs\In;

readonly class ListPublicTripsDTO
{
    public function __construct(
        public int $page = 1,
        public ?string $search = null,
        public ?string $tag = null,
        public ?string $destination = null,
        public string $tab = 'recent',
        public ?int $authUserId = null,
    ) {}
}
