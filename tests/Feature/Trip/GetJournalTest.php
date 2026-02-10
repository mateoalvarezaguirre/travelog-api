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
class GetJournalTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_journal_returns_200_when_found(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create([
            'owner_id'   => $user->id,
            'status'     => StatusEnum::PUBLISHED,
            'visibility' => VisibilityEnum::PUBLIC,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/journals/' . $trip->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id', 'title', 'content', 'excerpt', 'status', 'author',
            ]);
    }

    public function test_get_journal_returns_404_when_not_found(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/journals/non-existent-uuid');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Bitácora no encontrada']);
    }
}
