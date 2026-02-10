<?php

declare(strict_types=1);

namespace Src\Search\Infrastructure\Database\Repositories;

use App\Models\Place;
use App\Models\Trip;
use App\Models\User;
use Src\Search\Domain\Repositories\SearchRepository;
use Src\Trip\Domain\Enums\StatusEnum;
use Src\Trip\Domain\Enums\VisibilityEnum;

class SearchEloquentRepository implements SearchRepository
{
    /**
     * @return Trip[]
     */
    public function searchTrips(string $query, int $limit = 50): array
    {
        $term = '%' . trim($query) . '%';
        if ($term === '%%') {
            return [];
        }

        return Trip::query()
            ->with(['owner:id,name,username,avatar', 'tags:id,name', 'media' => fn ($q) => $q->where('is_visible', true)->orderBy('order')])
            ->where('status', StatusEnum::PUBLISHED)
            ->where('visibility', VisibilityEnum::PUBLIC)
            ->where(function ($q) use ($term): void {
                $q->where('title', 'like', $term)
                    ->orWhere('content', 'like', $term)
                    ->orWhere('location', 'like', $term);
            })
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->all();
    }

    /**
     * @return User[]
     */
    public function searchUsers(string $query, int $limit = 50): array
    {
        $term = '%' . trim($query) . '%';
        if ($term === '%%') {
            return [];
        }

        return User::query()
            ->select(['id', 'name', 'username', 'avatar'])
            ->where(function ($q) use ($term): void {
                $q->where('name', 'like', $term)
                    ->orWhere('username', 'like', $term);
            })
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->all();
    }

    /**
     * @return Place[]
     */
    public function searchPlaces(string $query, int $limit = 50): array
    {
        $term = '%' . trim($query) . '%';
        if ($term === '%%') {
            return [];
        }

        return Place::query()
            ->where('name', 'like', $term)
            ->orWhere('country', 'like', $term)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->all();
    }
}
