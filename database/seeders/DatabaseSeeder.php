<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Follow;
use App\Models\Like;
use App\Models\Place;
use App\Models\Tag;
use App\Models\Trip;
use App\Models\TripComment;
use App\Models\TripMedia;
use App\Models\User;
use App\Models\UserBiography;
use Illuminate\Database\Seeder;
use Src\Trip\Domain\Enums\StatusEnum;
use Src\Trip\Domain\Enums\VisibilityEnum;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
        ]);

        $mainUser = User::factory()->create([
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'username' => 'testuser',
        ]);
        UserBiography::factory()->create(['user_id' => $mainUser->id]);

        $users = User::factory(9)->create();
        $users->each(fn (User $user) => UserBiography::factory()->create(['user_id' => $user->id]));

        $allUsers = $users->push($mainUser);

        $tags = Tag::factory(8)->create();

        $allUsers->each(function (User $user) use ($tags): void {
            $tripCount = rand(2, 5);
            $trips     = Trip::factory($tripCount)->create([
                'owner_id'   => $user->id,
                'status'     => StatusEnum::PUBLISHED,
                'visibility' => VisibilityEnum::PUBLIC,
            ]);

            $trips->each(function (Trip $trip) use ($tags, $user): void {
                $trip->tags()->attach($tags->random(rand(1, 3))->pluck('id'));

                TripMedia::factory(rand(1, 3))->create([
                    'trip_id'     => $trip->id,
                    'uploaded_by' => $user->id,
                ]);

                TripComment::factory(rand(0, 4))->create([
                    'trip_id' => $trip->id,
                ]);

                $trip->update([
                    'comments_count' => $trip->comments()->count(),
                ]);
            });

            Trip::factory(rand(0, 2))->draft()->create([
                'owner_id' => $user->id,
            ]);

            Place::factory(rand(1, 4))->create([
                'user_id' => $user->id,
            ]);
        });

        $this->seedFollowRelationships($allUsers);
        $this->seedLikes($allUsers);
    }

    /**
     * @param \Illuminate\Support\Collection<int, User> $users
     */
    private function seedFollowRelationships($users): void
    {
        $users->each(function (User $user) use ($users): void {
            $toFollow = $users->where('id', '!=', $user->id)->random(rand(2, 5));
            $toFollow->each(function (User $target) use ($user): void {
                Follow::factory()->create([
                    'follower_id'  => $user->id,
                    'following_id' => $target->id,
                ]);
            });
        });
    }

    /**
     * @param \Illuminate\Support\Collection<int, User> $users
     */
    private function seedLikes($users): void
    {
        $publishedTrips = Trip::where('status', StatusEnum::PUBLISHED)->get();

        $users->each(function (User $user) use ($publishedTrips): void {
            $toLike = $publishedTrips->random(min($publishedTrips->count(), rand(3, 8)));
            $toLike->each(function (Trip $trip) use ($user): void {
                Like::factory()->create([
                    'trip_id' => $trip->id,
                    'user_id' => $user->id,
                ]);
                $trip->increment('likes_count');
            });
        });
    }
}
