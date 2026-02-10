<?php

declare(strict_types=1);

namespace Tests\Feature\Social;

use App\Models\Trip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class ListCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_comments_returns_empty_array_when_no_comments(): void
    {
        $trip = Trip::factory()->create();

        $response = $this->getJson('/api/journals/' . $trip->id . '/comments');

        $response->assertStatus(200)
            ->assertJson([]);
    }
}
