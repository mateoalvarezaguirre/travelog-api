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
class AddCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_comment_returns_201_when_authenticated(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/journals/' . $trip->id . '/comments', [
                'text' => 'Great trip!',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'text', 'user', 'createdAt']);
    }
}
