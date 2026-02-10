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
class ListPlacesTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_places_returns_array(): void
    {
        $user = User::factory()->create();
        Place::factory()->count(2)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/places');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'country', 'coordinates', 'markerType'],
            ]);
    }
}
