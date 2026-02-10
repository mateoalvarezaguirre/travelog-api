<?php

declare(strict_types=1);

namespace Tests\Feature\Trip;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class CreateJournalTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_journal_returns_201_when_authenticated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/journals', [
                'title'   => 'My Trip',
                'content' => '<p>Content</p>',
                'status'  => 'draft',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id', 'title', 'content', 'excerpt', 'status', 'author',
            ])
            ->assertJsonPath('title', 'My Trip');
    }
}
