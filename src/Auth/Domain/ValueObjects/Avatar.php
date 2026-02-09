<?php

namespace Src\Auth\Domain\ValueObjects;

readonly class Avatar
{
    public function __construct(
        public string $url
    ){}
}
