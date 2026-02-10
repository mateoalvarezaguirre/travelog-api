<?php

declare(strict_types=1);

namespace Src\Profile\Domain\ValueObjects;

readonly class UpdateProfileData
{
    public function __construct(
        public ?string $name = null,
        public ?string $location = null,
        public ?string $avatar = null,
        public ?string $coverPhoto = null,
        public ?string $bio = null,
    ) {}
}
