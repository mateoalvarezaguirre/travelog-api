<?php

namespace App\Providers\Contexts;

use Illuminate\Support\ServiceProvider;
use Src\Auth\Domain\Contracts\AuthManagement;
use Src\Auth\Infrastructure\Framework\Adapters\AuthLaravelAdapter;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthManagement::class, AuthLaravelAdapter::class);
    }
}
