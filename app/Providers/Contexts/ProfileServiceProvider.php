<?php

namespace App\Providers\Contexts;

use Illuminate\Support\ServiceProvider;
use Src\Profile\Domain\Repositories\UserRepository;
use Src\Profile\Infrastructure\Database\Repositories\UserEloquentRepository;

class ProfileServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepository::class, UserEloquentRepository::class);
    }
}
