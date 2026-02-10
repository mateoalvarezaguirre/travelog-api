<?php

declare(strict_types=1);

namespace Tests\Feature\Place;

use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class DeletePlaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_place_returns_204_when_owner(): void
    {
        $user  = User::factory()->create();
        $place = Place::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/places/' . $place->id);

        $response->assertStatus(204);
    }
}
