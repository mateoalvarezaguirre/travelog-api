<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class GetUserByUsernameTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_user_by_username_returns_profile_when_found(): void
    {
        $target = User::factory()->create(['username' => 'traveler']);
        $user   = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/users/traveler');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id', 'name', 'email', 'username', 'journalCount',
                'followersCount', 'followingCount', 'countriesVisited', 'isFollowing',
            ])
            ->assertJsonPath('username', 'traveler');
    }

    public function test_get_user_by_username_returns_404_when_not_found(): void
    {
        User::factory()->create();

        $response = $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/users/non-existent-username');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Usuario no encontrado']);
    }
}
