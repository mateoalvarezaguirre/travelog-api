<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Place\Infrastructure\Http\Controllers\CreatePlaceController;
use Src\Place\Infrastructure\Http\Controllers\DeletePlaceController;
use Src\Place\Infrastructure\Http\Controllers\ListPlacesController;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/places', ListPlacesController::class);
    Route::post('/places', CreatePlaceController::class);
    Route::delete('/places/{id}', DeletePlaceController::class);
});
