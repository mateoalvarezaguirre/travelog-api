<?php

declare(strict_types=1);

namespace Tests\Feature\Social;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class LikeTripTest extends TestCase
{
    use RefreshDatabase;

    public function test_like_trip_returns_likes_count_when_authenticated(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/journals/' . $trip->id . '/like');

        $response->assertStatus(200)
            ->assertJsonStructure(['likesCount']);
    }
}
