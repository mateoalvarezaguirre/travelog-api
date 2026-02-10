<?php

declare(strict_types=1);

namespace Tests\Feature\Search;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Trip\Domain\Enums\StatusEnum;
use Src\Trip\Domain\Enums\VisibilityEnum;
use Tests\TestCase;

/**
 * @internal
 */
class SearchJournalsTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_journals_returns_results(): void
    {
        $user = User::factory()->create();
        Trip::factory()->create([
            'owner_id'   => $user->id,
            'title'      => 'Amazing Tokyo Adventure',
            'status'     => StatusEnum::PUBLISHED,
            'visibility' => VisibilityEnum::PUBLIC,
        ]);

        $response = $this->getJson('/api/search/journals?q=Tokyo');

        $response->assertStatus(200)
            ->assertJsonIsArray();
    }
}
