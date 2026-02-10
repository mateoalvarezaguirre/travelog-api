<?php

declare(strict_types=1);

namespace Src\Profile\Infrastructure\Database\Repositories;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Src\Profile\Domain\Repositories\ProfileRepository;
use Src\Profile\Domain\ValueObjects\ProfileView;
use Src\Profile\Domain\ValueObjects\StatsView;
use Src\Profile\Domain\ValueObjects\UpdateProfileData;
use Src\Trip\Domain\Enums\StatusEnum;

class ProfileEloquentRepository implements ProfileRepository
{
    private const STATS_TTL = 900;

    public function findUserIdByUsername(string $username): ?int
    {
        $user = User::where('username', $username)->value('id');

        return $user !== null ? (int) $user : null;
    }

    public function getProfileView(int $userId, ?int $authUserId): ?ProfileView
    {
        $user = User::with('biography')->find($userId);
        if ($user === null) {
            return null;
        }

        $journalCount     = $user->trips()->where('status', StatusEnum::PUBLISHED)->count();
        $followersCount   = Follow::where('following_id', $user->id)->count();
        $followingCount   = Follow::where('follower_id', $user->id)->count();
        $countriesVisited = $user->places()
            ->where('marker_type', 'visited')
            ->distinct('country')
            ->count('country');

        $isFollowing = false;
        if ($authUserId !== null && $authUserId !== $user->id) {
            $isFollowing = Follow::where('follower_id', $authUserId)
                ->where('following_id', $user->id)
                ->exists();
        }

        return new ProfileView(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            username: $user->username,
            bio: $user->biography?->content,
            avatar: $user->avatar,
            coverPhoto: $user->cover_photo,
            location: $user->location,
            journalCount: $journalCount,
            followersCount: $followersCount,
            followingCount: $followingCount,
            countriesVisited: $countriesVisited,
            isFollowing: $isFollowing,
        );
    }

    public function getStatsView(int $userId): ?StatsView
    {
        $user = User::find($userId);
        if ($user === null) {
            return null;
        }

        return Cache::remember(
            "user_stats:{$userId}",
            self::STATS_TTL,
            fn () => $this->computeStatsView($user),
        );
    }

    public function updateProfile(int $userId, UpdateProfileData $data): ProfileView
    {
        $user = User::findOrFail($userId);

        $fields = array_filter([
            'name'        => $data->name,
            'location'    => $data->location,
            'avatar'      => $data->avatar,
            'cover_photo' => $data->coverPhoto,
        ], fn ($value) => $value !== null);

        if ($fields !== []) {
            $user->update($fields);
        }

        if ($data->bio !== null) {
            $user->biography()->updateOrCreate(
                ['user_id' => $user->id],
                ['content' => $data->bio]
            );
            $user->load('biography');
        }

        $user = $user->fresh();
        if ($user === null) {
            throw new \RuntimeException('User not found after update.');
        }

        Cache::forget("user_stats:{$userId}");

        $view = $this->getProfileView($user->id, $user->id);
        if ($view === null) {
            throw new \RuntimeException('Profile view could not be loaded.');
        }

        return $view;
    }

    private function computeStatsView(User $user): StatsView
    {
        $journalsWritten = $user->trips()->where('status', StatusEnum::PUBLISHED)->count();

        $visitedPlaces    = $user->places()->where('marker_type', 'visited');
        $countriesVisited = (clone $visitedPlaces)->distinct('country')->count('country');
        $citiesExplored   = (clone $visitedPlaces)->distinct('name')->count('name');

        $totalDistanceMeters = $user->trips()
            ->join('trip_routes', 'trips.id', '=', 'trip_routes.trip_id')
            ->sum('trip_routes.distance_meters');

        $totalDistanceKm = round($totalDistanceMeters / 1000);
        $totalDistance   = number_format($totalDistanceKm, 0, ',', '.') . ' km';

        $regions = $this->computeRegions($user);

        return new StatsView(
            totalDistance: $totalDistance,
            countriesVisited: $countriesVisited,
            citiesExplored: $citiesExplored,
            journalsWritten: $journalsWritten,
            regions: $regions,
        );
    }

    /**
     * @return array<int, array{name: string, percentage: int}>
     */
    private function computeRegions(User $user): array
    {
        $continentMap = [
            'Asia'              => ['AF', 'AM', 'AZ', 'BH', 'BD', 'BT', 'BN', 'KH', 'CN', 'CY', 'GE', 'IN', 'ID', 'IR', 'IQ', 'IL', 'JP', 'JO', 'KZ', 'KW', 'KG', 'LA', 'LB', 'MY', 'MV', 'MN', 'MM', 'NP', 'KP', 'OM', 'PK', 'PS', 'PH', 'QA', 'SA', 'SG', 'KR', 'LK', 'SY', 'TW', 'TJ', 'TH', 'TL', 'TR', 'TM', 'AE', 'UZ', 'VN', 'YE'],
            'Europa'            => ['AL', 'AD', 'AT', 'BY', 'BE', 'BA', 'BG', 'HR', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IS', 'IE', 'IT', 'XK', 'LV', 'LI', 'LT', 'LU', 'MT', 'MD', 'MC', 'ME', 'NL', 'MK', 'NO', 'PL', 'PT', 'RO', 'RU', 'SM', 'RS', 'SK', 'SI', 'ES', 'SE', 'CH', 'UA', 'GB', 'VA'],
            'Africa'            => ['DZ', 'AO', 'BJ', 'BW', 'BF', 'BI', 'CM', 'CV', 'CF', 'TD', 'KM', 'CD', 'CG', 'CI', 'DJ', 'EG', 'GQ', 'ER', 'SZ', 'ET', 'GA', 'GM', 'GH', 'GN', 'GW', 'KE', 'LS', 'LR', 'LY', 'MG', 'MW', 'ML', 'MR', 'MU', 'MA', 'MZ', 'NA', 'NE', 'NG', 'RW', 'ST', 'SN', 'SC', 'SL', 'SO', 'ZA', 'SS', 'SD', 'TZ', 'TG', 'TN', 'UG', 'ZM', 'ZW'],
            'América del Norte' => ['CA', 'MX', 'US'],
            'América del Sur'   => ['AR', 'BO', 'BR', 'CL', 'CO', 'EC', 'GY', 'PY', 'PE', 'SR', 'UY', 'VE'],
            'Oceanía'           => ['AU', 'FJ', 'KI', 'MH', 'FM', 'NR', 'NZ', 'PW', 'PG', 'WS', 'SB', 'TO', 'TV', 'VU'],
        ];

        $codeToContinent = [];
        foreach ($continentMap as $continent => $codes) {
            foreach ($codes as $code) {
                $codeToContinent[$code] = $continent;
            }
        }

        $countryCodes = $user->places()
            ->where('marker_type', 'visited')
            ->join('countries', function ($join): void {
                $join->on('places.country', '=', 'countries.country_name');
            })
            ->pluck('countries.alpha2_code')
            ->toArray();

        if ($countryCodes === []) {
            return [];
        }

        $regionCounts = [];
        foreach ($countryCodes as $code) {
            $continent                = $codeToContinent[$code] ?? 'Otros';
            $regionCounts[$continent] = ($regionCounts[$continent] ?? 0) + 1;
        }

        $total   = array_sum($regionCounts);
        $regions = [];
        foreach ($regionCounts as $name => $count) {
            $regions[] = [
                'name'       => $name,
                'percentage' => (int) round(($count / $total) * 100),
            ];
        }

        usort($regions, fn ($a, $b) => $b['percentage'] <=> $a['percentage']);

        return $regions;
    }
}
