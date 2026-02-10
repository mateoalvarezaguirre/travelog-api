<?php

declare(strict_types=1);

namespace Tests\Feature\Social;

use App\Models\Like;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class UnlikeTripTest extends TestCase
{
    use RefreshDatabase;

    public function test_unlike_trip_returns_likes_count(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create();

        Like::factory()->create([
            'trip_id' => $trip->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/journals/' . $trip->id . '/unlike');

        $response->assertStatus(200)
            ->assertJsonStructure(['likesCount']);
    }
}
