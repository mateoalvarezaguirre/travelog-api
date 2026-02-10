<?php

declare(strict_types=1);

namespace Tests\Unit\Trip;

use Src\Trip\Domain\Enums\StatusEnum;
use Tests\TestCase;

/**
 * @internal
 */
class StatusEnumTest extends TestCase
{
    public function test_enum_has_exactly_three_cases(): void
    {
        $cases = StatusEnum::cases();

        $this->assertCount(3, $cases);
    }

    public function test_enum_values_are_lowercase_strings(): void
    {
        $this->assertSame('draft', StatusEnum::DRAFT->value);
        $this->assertSame('published', StatusEnum::PUBLISHED->value);
        $this->assertSame('archived', StatusEnum::ARCHIVED->value);
    }

    public function test_enum_can_be_created_from_valid_string(): void
    {
        $this->assertSame(StatusEnum::DRAFT, StatusEnum::from('draft'));
        $this->assertSame(StatusEnum::PUBLISHED, StatusEnum::from('published'));
        $this->assertSame(StatusEnum::ARCHIVED, StatusEnum::from('archived'));
    }
}
