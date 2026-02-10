<?php

declare(strict_types=1);

namespace Src\Social\Infrastructure\Observers;

use App\Models\Like;
use App\Models\Trip;

class LikeObserver
{
    public function created(Like $like): void
    {
        Trip::where('id', $like->trip_id)->increment('likes_count');
    }

    public function deleted(Like $like): void
    {
        Trip::where('id', $like->trip_id)->decrement('likes_count');
    }
}
