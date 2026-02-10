<?php

declare(strict_types=1);

namespace Src\Trip\Infrastructure\Database\Repositories;

use App\Models\Like;
use App\Models\Tag;
use App\Models\Trip;
use App\Models\TripMedia;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Src\Shared\Core\Domain\ValueObjects\PaginationResult;
use Src\Trip\Domain\Entities\TripEntity;
use Src\Trip\Domain\Enums\StatusEnum;
use Src\Trip\Domain\Enums\VisibilityEnum;
use Src\Trip\Domain\Repositories\TripRepository;
use Src\Trip\Domain\ValueObjects\Engagement;
use Src\Trip\Domain\ValueObjects\Owner;
use Src\Trip\Domain\ValueObjects\PublicTripFilters;
use Src\Trip\Domain\ValueObjects\TripFilters;

class TripEloquentRepository implements TripRepository
{
    private const PER_PAGE                 = 12;
    private const PUBLIC_FEED_TTL          = 120;
    private const PUBLIC_FEED_CACHE_PREFIX = 'public_feed:';

    public function findById(string $id): ?TripEntity
    {
        $trip = Trip::with([
            'owner:id,name,username,avatar',
            'tags:id,name',
            'media' => fn ($q) => $q->where('is_visible', true)->orderBy('order'),
            'comments.user:id,name,username,avatar',
        ])->find($id);

        if ($trip === null) {
            return null;
        }

        return $this->toEntity($trip);
    }

    public function findByOwner(int $ownerId, TripFilters $filters): PaginationResult
    {
        $query = Trip::with([
            'owner:id,name,username,avatar',
            'tags:id,name',
            'media' => fn ($q) => $q->where('is_visible', true)->orderBy('order'),
        ])->select([
            'id', 'title', 'excerpt', 'owner_id', 'status', 'visibility',
            'date', 'location', 'latitude', 'longitude',
            'likes_count', 'comments_count', 'created_at', 'updated_at',
        ]);

        $query = match ($filters->tab) {
            'favorites' => $query->whereIn('id', function ($sub) use ($ownerId): void {
                $sub->select('trip_id')->from('likes')->where('user_id', $ownerId);
            }),
            'shared' => $query->whereIn('id', function ($sub) use ($ownerId): void {
                $sub->select('trip_id')->from('trip_comments')
                    ->where('user_id', $ownerId);
            })->where('owner_id', '!=', $ownerId),
            default => $query->where('owner_id', $ownerId),
        };

        $this->applyCommonFilters($query, $filters->search, $filters->tag, $filters->status);

        $paginator = $query->orderByDesc('created_at')->paginate(self::PER_PAGE, ['*'], 'page', $filters->page);

        return new PaginationResult(
            items: $paginator->items(),
            currentPage: $paginator->currentPage(),
            lastPage: $paginator->lastPage(),
            perPage: $paginator->perPage(),
            total: $paginator->total(),
        );
    }

    public function findPublic(PublicTripFilters $filters): PaginationResult
    {
        $cacheKey = $this->buildPublicFeedCacheKey($filters);

        return Cache::remember($cacheKey, self::PUBLIC_FEED_TTL, fn (): PaginationResult => $this->queryPublicFeed($filters));
    }

    public static function clearPublicFeedCache(): void
    {
        $prefix = self::PUBLIC_FEED_CACHE_PREFIX;
        Cache::forget($prefix);
    }

    public function save(TripEntity $trip): TripEntity
    {
        $model = Trip::create([
            'id'           => $trip->getUuid(),
            'title'        => $trip->title,
            'content'      => $trip->content,
            'excerpt'      => $trip->getExcerpt(),
            'owner_id'     => $trip->owner->id,
            'status'       => $trip->status->value,
            'visibility'   => $trip->visibility->value,
            'published_at' => $trip->publishedAt,
            'date'         => $trip->getDate(),
            'location'     => $trip->getLocation(),
            'latitude'     => $trip->getLatitude(),
            'longitude'    => $trip->getLongitude(),
        ]);

        $this->syncTags($model, $trip->getTags());
        $this->syncMedia($model, $trip->getImageIds());

        $model->load(['owner:id,name,username,avatar', 'tags:id,name', 'media']);

        return $this->toEntity($model);
    }

    public function update(string $id, array $data): TripEntity
    {
        $model = Trip::findOrFail($id);

        $attributes = [];
        foreach (['title', 'content', 'excerpt', 'date', 'location', 'latitude', 'longitude', 'published_at'] as $field) {
            if (array_key_exists($field, $data)) {
                $attributes[$field] = $data[$field];
            }
        }
        if (array_key_exists('status', $data)) {
            $attributes['status'] = $data['status'] instanceof StatusEnum ? $data['status']->value : $data['status'];
        }
        if (array_key_exists('visibility', $data)) {
            $attributes['visibility'] = $data['visibility'] instanceof VisibilityEnum ? $data['visibility']->value : $data['visibility'];
        }

        if (! empty($attributes)) {
            $model->update($attributes);
        }

        $model->load(['owner:id,name,username,avatar', 'tags:id,name', 'media']);

        return $this->toEntity($model);
    }

    public function delete(string $id): void
    {
        Trip::where('id', $id)->delete();
    }

    /**
     * @param string[] $tagNames
     */
    public function syncTags(Trip $trip, array $tagNames): void
    {
        if (empty($tagNames)) {
            return;
        }

        $tagIds = [];
        foreach ($tagNames as $name) {
            $tag      = Tag::firstOrCreate(['name' => strtolower(trim($name))]);
            $tagIds[] = $tag->id;
        }

        $trip->tags()->sync($tagIds);
    }

    /**
     * @param int[] $mediaIds
     */
    public function syncMedia(Trip $trip, array $mediaIds): void
    {
        if (empty($mediaIds)) {
            return;
        }

        TripMedia::whereIn('id', $mediaIds)
            ->whereNull('trip_id')
            ->update(['trip_id' => $trip->id]);
    }

    /**
     * @param string[] $tripIds
     *
     * @return string[]
     */
    public function getLikedTripIds(int $userId, array $tripIds): array
    {
        if (empty($tripIds)) {
            return [];
        }

        return Like::where('user_id', $userId)
            ->whereIn('trip_id', $tripIds)
            ->pluck('trip_id')
            ->toArray();
    }

    public function isLikedByUser(string $tripId, int $userId): bool
    {
        return Like::where('trip_id', $tripId)->where('user_id', $userId)->exists();
    }

    /**
     * @param string[] $tagNames
     */
    public function syncTagsByTripId(string $tripId, array $tagNames): void
    {
        $trip = Trip::find($tripId);
        if ($trip !== null) {
            $this->syncTags($trip, $tagNames);
        }
    }

    /**
     * @param int[] $mediaIds
     */
    public function syncMediaByTripId(string $tripId, array $mediaIds): void
    {
        $trip = Trip::find($tripId);
        if ($trip !== null) {
            $this->syncMedia($trip, $mediaIds);
        }
    }

    private function queryPublicFeed(PublicTripFilters $filters): PaginationResult
    {
        $query = Trip::with([
            'owner:id,name,username,avatar',
            'tags:id,name',
            'media' => fn ($q) => $q->where('is_visible', true)->orderBy('order'),
        ])->select([
            'id', 'title', 'excerpt', 'owner_id', 'status', 'visibility',
            'date', 'location', 'latitude', 'longitude',
            'likes_count', 'comments_count', 'created_at', 'updated_at',
        ])->published()->public();

        $query = match ($filters->tab) {
            'featured' => $query->where('likes_count', '>=', 10)->orderByDesc('likes_count'),
            'trending' => $query->withCount([
                'likes as recent_likes_count' => fn ($q) => $q->where('created_at', '>=', now()->subDays(7)),
            ])->orderByDesc('recent_likes_count'),
            'following' => $filters->authUserId !== null
                ? $query->whereIn('owner_id', function ($sub) use ($filters): void {
                    $sub->select('following_id')->from('follows')->where('follower_id', $filters->authUserId);
                })
                : $query,
            default => $query->orderByDesc('created_at'),
        };

        if ($filters->tab !== 'featured' && $filters->tab !== 'trending') {
            $query->orderByDesc('created_at');
        }

        $this->applyCommonFilters($query, $filters->search, $filters->tag);

        if ($filters->destination !== null) {
            $query->where('location', 'like', '%' . $filters->destination . '%');
        }

        $paginator = $query->paginate(self::PER_PAGE, ['*'], 'page', $filters->page);

        return new PaginationResult(
            items: $paginator->items(),
            currentPage: $paginator->currentPage(),
            lastPage: $paginator->lastPage(),
            perPage: $paginator->perPage(),
            total: $paginator->total(),
        );
    }

    private function buildPublicFeedCacheKey(PublicTripFilters $filters): string
    {
        return self::PUBLIC_FEED_CACHE_PREFIX . md5(serialize([
            'tab'         => $filters->tab,
            'page'        => $filters->page,
            'search'      => $filters->search,
            'tag'         => $filters->tag,
            'destination' => $filters->destination,
        ]));
    }

    private function toEntity(Trip $model): TripEntity
    {
        $owner = new Owner($model->owner_id);
        if ($model->relationLoaded('owner') && $model->owner !== null) {
            $owner->setName($model->owner->name);
            $owner->setProfilePicture($model->owner->avatar);
            $owner->setUsername($model->owner->username);
        }

        $engagement = new Engagement();
        $engagement->addLikes((int) ($model->likes_count ?? 0));
        $engagement->addComments((int) ($model->comments_count ?? 0));

        $entity = new TripEntity(
            title: $model->title,
            content: $model->content,
            owner: $owner,
            status: StatusEnum::from($model->status),
            visibility: VisibilityEnum::from($model->visibility),
            engagement: $engagement,
            createdAt: $model->created_at ?? now(),
            publishedAt: $model->published_at ? Carbon::parse($model->published_at) : null,
            privateContent: $model->private_content,
        );

        $entity->setUuid($model->id);
        $entity->setExcerpt($model->excerpt);
        $entity->setDate($model->date ? Carbon::parse($model->date) : null);
        $entity->setLocation($model->location);
        $entity->setLatitude($model->latitude);
        $entity->setLongitude($model->longitude);
        $entity->setUpdatedAt($model->updated_at);

        if ($model->relationLoaded('tags')) {
            $entity->setTags($model->tags->pluck('name')->toArray());
        }

        if ($model->relationLoaded('media')) {
            $images = [];
            foreach ($model->media->where('is_visible', true)->sortBy('order') as $media) {
                $images[] = [
                    'id'      => $media->id,
                    'url'     => $media->media_url,
                    'caption' => $media->caption,
                    'order'   => $media->order,
                ];
            }
            $entity->setImages($images);
        }

        return $entity;
    }

    /**
     * @param Builder<Trip> $query
     */
    private function applyCommonFilters(Builder $query, ?string $search, ?string $tag, ?string $status = null): void
    {
        if ($search !== null && $search !== '') {
            $query->where(function (Builder $q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($tag !== null && $tag !== '') {
            $query->whereHas('tags', fn (Builder $q) => $q->where('name', $tag));
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }
    }
}
