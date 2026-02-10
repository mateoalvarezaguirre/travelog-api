<?php

declare(strict_types=1);

namespace App\Providers\Contexts;

use Illuminate\Support\ServiceProvider;
use Src\Profile\Application\UseCases\GetAuthProfileUseCase;
use Src\Profile\Application\UseCases\GetProfileByUsernameUseCase;
use Src\Profile\Application\UseCases\GetStatsUseCase;
use Src\Profile\Application\UseCases\GetUserStatsUseCase;
use Src\Profile\Application\UseCases\UpdateProfileUseCase;
use Src\Profile\Domain\Repositories\ProfileRepository;
use Src\Profile\Domain\Repositories\UserRepository;
use Src\Profile\Infrastructure\Database\Repositories\ProfileEloquentRepository;
use Src\Profile\Infrastructure\Database\Repositories\UserEloquentRepository;

class ProfileServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepository::class, UserEloquentRepository::class);
        $this->app->bind(ProfileRepository::class, ProfileEloquentRepository::class);

        $this->app->bind(GetAuthProfileUseCase::class, fn ($app) => new GetAuthProfileUseCase(
            $app->make(ProfileRepository::class),
        ));
        $this->app->bind(GetProfileByUsernameUseCase::class, fn ($app) => new GetProfileByUsernameUseCase(
            $app->make(ProfileRepository::class),
        ));
        $this->app->bind(GetStatsUseCase::class, fn ($app) => new GetStatsUseCase(
            $app->make(ProfileRepository::class),
        ));
        $this->app->bind(GetUserStatsUseCase::class, fn ($app) => new GetUserStatsUseCase(
            $app->make(ProfileRepository::class),
        ));
        $this->app->bind(UpdateProfileUseCase::class, fn ($app) => new UpdateProfileUseCase(
            $app->make(ProfileRepository::class),
        ));
    }
}
