<?php

namespace App\Providers\Contexts;

use Illuminate\Support\ServiceProvider;
use Src\Auth\Domain\Contracts\AuthManagement;
use Src\Auth\Domain\Contracts\GoogleManagement;
use Src\Auth\Domain\Repositories\UserRepository;
use Src\Auth\Infrastructure\Database\Repositories\UserEloquentRepository;
use Src\Auth\Infrastructure\External\Adapters\GoogleAuthExternalAdapter;
use Src\Auth\Infrastructure\Framework\Adapters\AuthLaravelAdapter;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthManagement::class, AuthLaravelAdapter::class);
        $this->app->bind(UserRepository::class, UserEloquentRepository::class);
        $this->app->bind(GoogleManagement::class, GoogleAuthExternalAdapter::class);
    }
}
