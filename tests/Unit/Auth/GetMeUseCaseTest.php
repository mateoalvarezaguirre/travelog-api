<?php

declare(strict_types=1);

namespace Tests\Unit\Auth;

use App\Models\User;
use Tests\TestCase;

/**
 * @internal
 */
class GetMeUseCaseTest extends TestCase
{
    public function test_invoke_returns_user_array(): void
    {
        $user           = new User();
        $user->id       = 1;
        $user->name     = 'Test';
        $user->email    = 'test@example.com';
        $user->username = 'test';
        $user->avatar   = null;

        $useCase = new \Src\Auth\Application\UseCases\GetMeUseCase();

        $result = $useCase($user);

        $this->assertSame(1, $result['id']);
        $this->assertSame('Test', $result['name']);
        $this->assertSame('test@example.com', $result['email']);
        $this->assertSame('test', $result['username']);
    }
}
