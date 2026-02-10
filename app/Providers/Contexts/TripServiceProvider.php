<?php

declare(strict_types=1);

namespace App\Providers\Contexts;

use Illuminate\Support\ServiceProvider;
use Src\Trip\Application\UseCases\CreateTripUseCase;
use Src\Trip\Application\UseCases\DeleteTripUseCase;
use Src\Trip\Application\UseCases\GetTripUseCase;
use Src\Trip\Application\UseCases\ListPublicTripsUseCase;
use Src\Trip\Application\UseCases\ListTripsUseCase;
use Src\Trip\Application\UseCases\RegisterMediaUseCase;
use Src\Trip\Application\UseCases\UpdateTripUseCase;
use Src\Trip\Domain\Repositories\TripMediaRepository;
use Src\Trip\Domain\Repositories\TripRepository;
use Src\Trip\Infrastructure\Database\Repositories\TripEloquentRepository;
use Src\Trip\Infrastructure\Database\Repositories\TripMediaEloquentRepository;

class TripServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TripRepository::class, TripEloquentRepository::class);
        $this->app->bind(TripMediaRepository::class, TripMediaEloquentRepository::class);

        $this->app->bind(CreateTripUseCase::class, fn ($app) => new CreateTripUseCase(
            $app->make(TripRepository::class),
        ));

        $this->app->bind(UpdateTripUseCase::class, fn ($app) => new UpdateTripUseCase(
            $app->make(TripRepository::class),
        ));

        $this->app->bind(DeleteTripUseCase::class, fn ($app) => new DeleteTripUseCase(
            $app->make(TripRepository::class),
        ));

        $this->app->bind(GetTripUseCase::class, fn ($app) => new GetTripUseCase(
            $app->make(TripRepository::class),
        ));

        $this->app->bind(ListTripsUseCase::class, fn ($app) => new ListTripsUseCase(
            $app->make(TripRepository::class),
        ));

        $this->app->bind(ListPublicTripsUseCase::class, fn ($app) => new ListPublicTripsUseCase(
            $app->make(TripRepository::class),
        ));

        $this->app->bind(RegisterMediaUseCase::class, fn ($app) => new RegisterMediaUseCase(
            $app->make(TripMediaRepository::class),
        ));
    }
}
