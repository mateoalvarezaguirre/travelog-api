<?php

declare(strict_types=1);

namespace Tests\Feature\Social;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class UnfollowUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_unfollow_user_returns_204(): void
    {
        $user   = User::factory()->create();
        $target = User::factory()->create();

        Follow::factory()->create([
            'follower_id'  => $user->id,
            'following_id' => $target->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/users/' . $target->id . '/unfollow');

        $response->assertStatus(204);
    }
}
