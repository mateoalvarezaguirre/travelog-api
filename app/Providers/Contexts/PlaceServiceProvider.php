<?php

declare(strict_types=1);

namespace App\Providers\Contexts;

use Illuminate\Support\ServiceProvider;
use Src\Place\Application\UseCases\CreatePlaceUseCase;
use Src\Place\Application\UseCases\DeletePlaceUseCase;
use Src\Place\Application\UseCases\ListPlacesUseCase;
use Src\Place\Domain\Repositories\PlaceRepository;
use Src\Place\Infrastructure\Database\Repositories\PlaceEloquentRepository;

class PlaceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PlaceRepository::class, PlaceEloquentRepository::class);

        $this->app->bind(CreatePlaceUseCase::class, fn ($app) => new CreatePlaceUseCase(
            $app->make(PlaceRepository::class),
        ));
        $this->app->bind(ListPlacesUseCase::class, fn ($app) => new ListPlacesUseCase(
            $app->make(PlaceRepository::class),
        ));
        $this->app->bind(DeletePlaceUseCase::class, fn ($app) => new DeletePlaceUseCase(
            $app->make(PlaceRepository::class),
        ));
    }
}
