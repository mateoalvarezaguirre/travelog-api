<?php

declare(strict_types=1);

namespace App\Providers\Contexts;

use Illuminate\Support\ServiceProvider;
use Src\Search\Application\UseCases\SearchJournalsUseCase;
use Src\Search\Application\UseCases\SearchPlacesUseCase;
use Src\Search\Application\UseCases\SearchUsersUseCase;
use Src\Search\Domain\Repositories\SearchRepository;
use Src\Search\Infrastructure\Database\Repositories\SearchEloquentRepository;

class SearchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SearchRepository::class, SearchEloquentRepository::class);

        $this->app->bind(SearchJournalsUseCase::class, fn ($app) => new SearchJournalsUseCase(
            $app->make(SearchRepository::class),
        ));
        $this->app->bind(SearchUsersUseCase::class, fn ($app) => new SearchUsersUseCase(
            $app->make(SearchRepository::class),
        ));
        $this->app->bind(SearchPlacesUseCase::class, fn ($app) => new SearchPlacesUseCase(
            $app->make(SearchRepository::class),
        ));
    }
}
