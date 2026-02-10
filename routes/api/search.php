<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Search\Infrastructure\Http\Controllers\SearchJournalsController;
use Src\Search\Infrastructure\Http\Controllers\SearchPlacesController;
use Src\Search\Infrastructure\Http\Controllers\SearchUsersController;

Route::middleware('optional_auth')->group(function (): void {
    Route::get('/search/journals', SearchJournalsController::class);
    Route::get('/search/users', SearchUsersController::class);
    Route::get('/search/places', SearchPlacesController::class);
});
