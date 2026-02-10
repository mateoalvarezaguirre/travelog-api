<?php

declare(strict_types=1);

namespace Tests\Feature\Trip;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class ListPublicJournalsTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_public_journals_returns_paginated_response(): void
    {
        $response = $this->getJson('/api/journals/public');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'currentPage',
                    'lastPage',
                    'perPage',
                    'total',
                ],
            ]);
    }
}
