<?php

declare(strict_types=1);

namespace Tests\Feature\Trip;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Trip\Domain\Enums\StatusEnum;
use Src\Trip\Domain\Enums\VisibilityEnum;
use Tests\TestCase;

/**
 * @internal
 */
class UpdateJournalTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_journal_returns_200_when_owner(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create([
            'owner_id'   => $user->id,
            'status'     => StatusEnum::PUBLISHED,
            'visibility' => VisibilityEnum::PUBLIC,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/journals/' . $trip->id, [
                'title' => 'Updated Title',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id', 'title', 'content', 'excerpt', 'status', 'author',
            ])
            ->assertJsonPath('title', 'Updated Title');
    }
}
