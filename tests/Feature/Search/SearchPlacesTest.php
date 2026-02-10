<?php

declare(strict_types=1);

namespace Tests\Feature\Search;

use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class SearchPlacesTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_places_returns_results(): void
    {
        $user = User::factory()->create();
        Place::factory()->create([
            'user_id' => $user->id,
            'name'    => 'Tokyo Tower',
        ]);

        $response = $this->getJson('/api/search/places?q=Tokyo');

        $response->assertStatus(200)
            ->assertJsonIsArray();
    }
}
