<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class UpdateProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_profile_returns_updated_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/profile', [
                'name'     => 'Updated Name',
                'location' => 'Madrid, Spain',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id', 'name', 'email', 'username', 'journalCount',
                'followersCount', 'followingCount', 'countriesVisited', 'isFollowing',
            ])
            ->assertJsonPath('name', 'Updated Name');
    }
}
