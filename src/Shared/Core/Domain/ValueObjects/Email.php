<?php

namespace Src\Shared\Core\Domain\ValueObjects;

readonly class Email
{
    public string $value;

    public function __construct(
        string $value
    ) {
        $this->validate($value);

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function validate(string $value): void
    {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email format: {$value}");
        }
    }
}
