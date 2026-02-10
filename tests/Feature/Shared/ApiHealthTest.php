<?php

declare(strict_types=1);

namespace Tests\Feature\Shared;

use Tests\TestCase;

/**
 * @internal
 */
class ApiHealthTest extends TestCase
{
    public function test_health_endpoint_returns_ok(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }
}
