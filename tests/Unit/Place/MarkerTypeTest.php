<?php

declare(strict_types=1);

namespace Tests\Unit\Place;

use Src\Place\Domain\Enums\MarkerTypeEnum;
use Tests\TestCase;

/**
 * @internal
 */
class MarkerTypeTest extends TestCase
{
    public function test_enum_has_exactly_three_cases(): void
    {
        $cases = MarkerTypeEnum::cases();

        $this->assertCount(3, $cases);
    }

    public function test_enum_values_are_lowercase_strings(): void
    {
        $this->assertSame('visited', MarkerTypeEnum::VISITED->value);
        $this->assertSame('planned', MarkerTypeEnum::PLANNED->value);
        $this->assertSame('wishlist', MarkerTypeEnum::WISHLIST->value);
    }

    public function test_enum_can_be_created_from_valid_string(): void
    {
        $this->assertSame(MarkerTypeEnum::VISITED, MarkerTypeEnum::from('visited'));
        $this->assertSame(MarkerTypeEnum::PLANNED, MarkerTypeEnum::from('planned'));
        $this->assertSame(MarkerTypeEnum::WISHLIST, MarkerTypeEnum::from('wishlist'));
    }
}
