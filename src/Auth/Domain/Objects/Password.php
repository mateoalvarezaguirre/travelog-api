<?php

namespace Src\Auth\Domain\Objects;

use Illuminate\Support\Facades\Hash;

class Password
{
    public function __construct(
        private string $value,
    ) {
        if (! $this->isHashed()) {
            $this->hash();
        }
    }

    public function getHash(): string
    {
        return $this->value;
    }

    public function isHashed(): bool
    {
        return Hash::isHashed($this->value);
    }

    public function check(string $plainPassword): bool
    {
        return Hash::check($this->value, $plainPassword);
    }

    private function hash(): void
    {
        $this->value = Hash::make($this->value);
    }
}
