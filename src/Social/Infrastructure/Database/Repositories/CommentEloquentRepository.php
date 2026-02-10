<?php

declare(strict_types=1);

namespace Src\Social\Infrastructure\Database\Repositories;

use App\Models\TripComment;
use Illuminate\Support\Str;
use Src\Social\Domain\Repositories\CommentRepository;

class CommentEloquentRepository implements CommentRepository
{
    /**
     * @return TripComment[]
     */
    public function findByTrip(string $tripId): array
    {
        return TripComment::with('user:id,name,username,avatar')
            ->where('trip_id', $tripId)
            ->orderByDesc('created_at')
            ->get()
            ->all();
    }

    public function create(string $tripId, int $userId, string $text): TripComment
    {
        $comment = TripComment::create([
            'id'      => Str::uuid()->toString(),
            'trip_id' => $tripId,
            'user_id' => $userId,
            'text'    => $text,
        ]);

        $comment->load('user:id,name,username,avatar');

        return $comment;
    }
}
