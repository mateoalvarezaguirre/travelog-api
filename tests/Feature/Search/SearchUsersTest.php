<?php

declare(strict_types=1);

namespace Tests\Feature\Search;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class SearchUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_users_returns_results(): void
    {
        User::factory()->create(['name' => 'John Traveler']);

        $response = $this->getJson('/api/search/users?q=John');

        $response->assertStatus(200)
            ->assertJsonIsArray();
    }
}
