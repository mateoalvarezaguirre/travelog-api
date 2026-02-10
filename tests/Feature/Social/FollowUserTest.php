<?php

declare(strict_types=1);

namespace Tests\Feature\Social;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class FollowUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_follow_user_returns_204(): void
    {
        $user   = User::factory()->create();
        $target = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/users/' . $target->id . '/follow');

        $response->assertStatus(204);
    }
}
