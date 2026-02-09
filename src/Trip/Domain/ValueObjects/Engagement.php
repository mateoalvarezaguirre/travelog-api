<?php

namespace Src\Trip\Domain\ValueObjects;

class Engagement
{
    public function __construct(
        private int $likes = 0,
        private int $comments = 0,
        private int $shares = 0,
        private int $views = 0,
    ) {}

    public function getLikesCount(): int
    {
        return $this->likes;
    }

    public function addLikes(int $likes): void
    {
        $this->likes += $likes;
    }

    public function addLike(): void
    {
        $this->likes++;
    }

    public function getCommentsCount(): int
    {
        return $this->comments;
    }

    public function addComments(int $comments): void
    {
        $this->comments += $comments;
    }

    public function addComment(): void
    {
        $this->comments++;
    }

    public function getSharesCount(): int
    {
        return $this->shares;
    }

    public function addShares(int $shares): void
    {
        $this->shares += $shares;
    }

    public function addShare(): void
    {
        $this->shares++;
    }

    public function getViewsCount(): int
    {
        return $this->views;
    }

    public function addViews(int $views): void
    {
        $this->views += $views;
    }

    public function addView(): void
    {
        $this->views++;
    }
}
