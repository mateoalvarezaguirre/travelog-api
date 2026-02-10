<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Profile\Infrastructure\Http\Controllers\GetProfileController;
use Src\Profile\Infrastructure\Http\Controllers\GetStatsController;
use Src\Profile\Infrastructure\Http\Controllers\GetUserByUsernameController;
use Src\Profile\Infrastructure\Http\Controllers\GetUserStatsController;
use Src\Profile\Infrastructure\Http\Controllers\UpdateProfileController;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/profile', GetProfileController::class);
    Route::put('/profile', UpdateProfileController::class);
    Route::get('/profile/stats', GetStatsController::class);

    Route::get('/users/{username}', GetUserByUsernameController::class);
    Route::get('/users/{username}/stats', GetUserStatsController::class);
});
