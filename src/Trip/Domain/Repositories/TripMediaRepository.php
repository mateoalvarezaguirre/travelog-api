<?php

declare(strict_types=1);

namespace Src\Trip\Domain\Repositories;

use Src\Trip\Domain\ValueObjects\RegisteredMedia;

interface TripMediaRepository
{
    public function register(int $uploadedBy, string $url, ?string $caption): RegisteredMedia;
}
