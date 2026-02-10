<?php

declare(strict_types=1);

namespace Tests\Unit\Trip;

use Src\Trip\Domain\Enums\VisibilityEnum;
use Tests\TestCase;

/**
 * @internal
 */
class VisibilityEnumTest extends TestCase
{
    public function test_enum_has_exactly_three_cases(): void
    {
        $cases = VisibilityEnum::cases();

        $this->assertCount(3, $cases);
    }

    public function test_enum_values_are_lowercase_strings(): void
    {
        $this->assertSame('public', VisibilityEnum::PUBLIC->value);
        $this->assertSame('private', VisibilityEnum::PRIVATE->value);
        $this->assertSame('unlisted', VisibilityEnum::UNLISTED->value);
    }

    public function test_enum_can_be_created_from_valid_string(): void
    {
        $this->assertSame(VisibilityEnum::PUBLIC, VisibilityEnum::from('public'));
        $this->assertSame(VisibilityEnum::PRIVATE, VisibilityEnum::from('private'));
        $this->assertSame(VisibilityEnum::UNLISTED, VisibilityEnum::from('unlisted'));
    }
}
