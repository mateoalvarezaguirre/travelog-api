<?php

declare(strict_types=1);

namespace Tests\Unit\Shared;

use Src\Shared\Core\Domain\ValueObjects\Email;
use Tests\TestCase;

/**
 * @internal
 */
class EmailTest extends TestCase
{
    public function test_valid_email_is_constructed_successfully(): void
    {
        $email = new Email('user@example.com');

        $this->assertSame('user@example.com', $email->value);
    }

    public function test_to_string_returns_email_value(): void
    {
        $email = new Email('test@domain.org');

        $this->assertSame('test@domain.org', (string) $email);
    }

    public function test_invalid_email_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format: not-an-email');

        new Email('not-an-email');
    }

    public function test_empty_string_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format: ');

        new Email('');
    }

    public function test_email_is_readonly(): void
    {
        $email = new Email('immutable@test.com');

        $reflection = new \ReflectionClass(Email::class);

        $this->assertTrue($reflection->isReadOnly());
        $this->assertSame('immutable@test.com', $email->value);
    }
}
