<?php

namespace Src\Auth\Domain\ValueObjects;

use Src\Shared\Core\Domain\ValueObjects\Email;

readonly class Credentials
{
    public function __construct(
        public Email $email,
        public string $password
    ) {}
}
