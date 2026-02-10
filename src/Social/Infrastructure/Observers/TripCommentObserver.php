<?php

declare(strict_types=1);

namespace Src\Social\Infrastructure\Observers;

use App\Models\Trip;
use App\Models\TripComment;

class TripCommentObserver
{
    public function created(TripComment $comment): void
    {
        Trip::where('id', $comment->trip_id)->increment('comments_count');
    }

    public function deleted(TripComment $comment): void
    {
        Trip::where('id', $comment->trip_id)->decrement('comments_count');
    }
}
