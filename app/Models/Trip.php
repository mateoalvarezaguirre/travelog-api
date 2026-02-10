<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TripFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Src\Trip\Domain\Enums\StatusEnum;
use Src\Trip\Domain\Enums\VisibilityEnum;

class Trip extends Model
{
    /** @use HasFactory<TripFactory> */
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'title',
        'content',
        'excerpt',
        'private_content',
        'owner_id',
        'status',
        'visibility',
        'published_at',
        'date',
        'location',
        'latitude',
        'longitude',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return HasMany<TripComment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TripComment::class);
    }

    /**
     * @return HasMany<TripMedia, $this>
     */
    public function media(): HasMany
    {
        return $this->hasMany(TripMedia::class);
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'trip_tag');
    }

    /**
     * @return HasMany<Like, $this>
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * @return HasMany<TripLocation, $this>
     */
    public function locations(): HasMany
    {
        return $this->hasMany(TripLocation::class);
    }

    /**
     * @return HasMany<TripWaypoint, $this>
     */
    public function waypoints(): HasMany
    {
        return $this->hasMany(TripWaypoint::class);
    }

    /**
     * @return HasOne<TripRoute, $this>
     */
    public function route(): HasOne
    {
        return $this->hasOne(TripRoute::class);
    }

    /**
     * @param Builder<Trip> $query
     *
     * @return Builder<Trip>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', StatusEnum::PUBLISHED->value);
    }

    /**
     * @param Builder<Trip> $query
     *
     * @return Builder<Trip>
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visibility', VisibilityEnum::PUBLIC->value);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at'   => 'datetime',
            'date'           => 'date',
            'latitude'       => 'float',
            'longitude'      => 'float',
            'likes_count'    => 'integer',
            'comments_count' => 'integer',
        ];
    }
}
