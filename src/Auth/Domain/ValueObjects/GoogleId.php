<?php

namespace Src\Auth\Domain\ValueObjects;

readonly class GoogleId
{
    public function __construct(
        public string $value
    ){}
}
