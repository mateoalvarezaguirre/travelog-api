<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Trip\Infrastructure\Http\Controllers\CreateTripController;
use Src\Trip\Infrastructure\Http\Controllers\DeleteTripController;
use Src\Trip\Infrastructure\Http\Controllers\GetTripController;
use Src\Trip\Infrastructure\Http\Controllers\ListPublicTripsController;
use Src\Trip\Infrastructure\Http\Controllers\ListTripsController;
use Src\Trip\Infrastructure\Http\Controllers\RegisterMediaController;
use Src\Trip\Infrastructure\Http\Controllers\UpdateTripController;

Route::get('/journals/public', ListPublicTripsController::class);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/journals', ListTripsController::class);
    Route::get('/journals/{id}', GetTripController::class);
    Route::post('/journals', CreateTripController::class);
    Route::put('/journals/{id}', UpdateTripController::class);
    Route::delete('/journals/{id}', DeleteTripController::class);

    Route::post('/media', RegisterMediaController::class);
});
