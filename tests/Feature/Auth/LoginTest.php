<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_invalid_credentials_returns_401(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email'    => 'wrong@example.com',
            'password' => 'wrong',
        ]);

        $response->assertStatus(401);
    }
}
