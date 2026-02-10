<?php

declare(strict_types=1);

namespace Src\Trip\Infrastructure\Http\Resources;

use App\Models\Trip;
use Src\Shared\Infrastructure\Http\Resources\UserSummaryResource;

readonly class TripResource implements \JsonSerializable
{
    public function __construct(
        private Trip $trip,
        private bool $isLiked = false,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $coordinates = ($this->trip->latitude !== null && $this->trip->longitude !== null)
            ? ['lat' => $this->trip->latitude, 'lng' => $this->trip->longitude]
            : null;

        return [
            'id'          => $this->trip->id,
            'title'       => $this->trip->title,
            'content'     => $this->trip->content,
            'excerpt'     => $this->trip->excerpt,
            'date'        => $this->trip->date,
            'location'    => $this->trip->location,
            'coordinates' => $coordinates,
            'images'      => TripImageResource::collection(
                $this->trip->relationLoaded('media')
                    ? $this->trip->media->where('is_visible', true)->sortBy('order')
                    : collect()
            ),
            'tags' => $this->trip->relationLoaded('tags')
                ? $this->trip->tags->pluck('name')->toArray()
                : [],
            'status'        => $this->trip->status,
            'isPublic'      => $this->trip->visibility === 'public',
            'likesCount'    => $this->trip->likes_count,
            'commentsCount' => $this->trip->comments_count,
            'isLiked'       => $this->isLiked,
            'author'        => $this->trip->relationLoaded('owner') && $this->trip->owner
                ? (new UserSummaryResource($this->trip->owner))->jsonSerialize()
                : null,
            'createdAt' => $this->trip->created_at?->toIso8601String(),
            'updatedAt' => $this->trip->updated_at?->toIso8601String(),
        ];
    }
}
