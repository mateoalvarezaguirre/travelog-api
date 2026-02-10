<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class GetStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_stats_returns_stats_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/profile/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'totalDistance', 'countriesVisited', 'citiesExplored',
                'journalsWritten', 'regions',
            ]);
    }
}
