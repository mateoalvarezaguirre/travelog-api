<?php

declare(strict_types=1);

namespace Tests\Feature\Place;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class CreatePlaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_place_returns_201(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/places', [
                'name'        => 'Tokyo',
                'country'     => 'Japan',
                'date'        => '2025-06-15',
                'coordinates' => ['lat' => 35.6762, 'lng' => 139.6503],
                'markerType'  => 'visited',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'country', 'coordinates', 'markerType'])
            ->assertJsonPath('name', 'Tokyo');
    }
}
