<?php

declare(strict_types=1);

namespace Tests\Feature\Trip;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class DeleteJournalTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_journal_returns_204_when_owner(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create(['owner_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/journals/' . $trip->id);

        $response->assertStatus(204);
    }
}
