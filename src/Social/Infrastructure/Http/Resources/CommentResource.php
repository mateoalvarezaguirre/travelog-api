<?php

declare(strict_types=1);

namespace Src\Social\Infrastructure\Http\Resources;

use App\Models\TripComment;
use Src\Shared\Infrastructure\Http\Resources\UserSummaryResource;

readonly class CommentResource implements \JsonSerializable
{
    public function __construct(
        private TripComment $comment,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id'   => $this->comment->id,
            'text' => $this->comment->text,
            'user' => $this->comment->relationLoaded('user') && $this->comment->user
                ? (new UserSummaryResource($this->comment->user))->jsonSerialize()
                : null,
            'createdAt'  => $this->comment->created_at?->toIso8601String(),
            'likesCount' => 0,
        ];
    }

    /**
     * @param TripComment[] $comments
     *
     * @return array<int, array<string, mixed>>
     */
    public static function collection(array $comments): array
    {
        return array_map(
            fn (TripComment $comment) => (new self($comment))->jsonSerialize(),
            $comments
        );
    }
}
