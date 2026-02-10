<?php

declare(strict_types=1);

namespace Tests\Feature\Trip;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class ListJournalsTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_journals_returns_paginated_response_when_authenticated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/journals');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => ['currentPage', 'lastPage', 'perPage', 'total'],
            ]);
    }
}
