<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class GetProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_profile_returns_authenticated_user_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id', 'name', 'email', 'username', 'journalCount',
                'followersCount', 'followingCount', 'countriesVisited', 'isFollowing',
            ]);
    }
}
