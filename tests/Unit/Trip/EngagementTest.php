<?php

declare(strict_types=1);

namespace Tests\Unit\Trip;

use Src\Trip\Domain\ValueObjects\Engagement;
use Tests\TestCase;

/**
 * @internal
 */
class EngagementTest extends TestCase
{
    public function test_default_constructor_initializes_all_counts_to_zero(): void
    {
        $engagement = new Engagement();

        $this->assertSame(0, $engagement->getLikesCount());
        $this->assertSame(0, $engagement->getCommentsCount());
        $this->assertSame(0, $engagement->getSharesCount());
        $this->assertSame(0, $engagement->getViewsCount());
    }

    public function test_constructor_accepts_initial_values(): void
    {
        $engagement = new Engagement(likes: 10, comments: 5, shares: 3, views: 200);

        $this->assertSame(10, $engagement->getLikesCount());
        $this->assertSame(5, $engagement->getCommentsCount());
        $this->assertSame(3, $engagement->getSharesCount());
        $this->assertSame(200, $engagement->getViewsCount());
    }

    public function test_add_single_increments_each_counter_by_one(): void
    {
        $engagement = new Engagement();

        $engagement->addLike();
        $engagement->addComment();
        $engagement->addShare();
        $engagement->addView();

        $this->assertSame(1, $engagement->getLikesCount());
        $this->assertSame(1, $engagement->getCommentsCount());
        $this->assertSame(1, $engagement->getSharesCount());
        $this->assertSame(1, $engagement->getViewsCount());
    }

    public function test_add_bulk_increments_counters_by_given_amount(): void
    {
        $engagement = new Engagement(likes: 5, comments: 2, shares: 1, views: 50);

        $engagement->addLikes(10);
        $engagement->addComments(8);
        $engagement->addShares(3);
        $engagement->addViews(100);

        $this->assertSame(15, $engagement->getLikesCount());
        $this->assertSame(10, $engagement->getCommentsCount());
        $this->assertSame(4, $engagement->getSharesCount());
        $this->assertSame(150, $engagement->getViewsCount());
    }

    public function test_multiple_add_calls_accumulate_correctly(): void
    {
        $engagement = new Engagement();

        $engagement->addLike();
        $engagement->addLike();
        $engagement->addLike();
        $engagement->addLikes(7);

        $this->assertSame(10, $engagement->getLikesCount());
    }
}
