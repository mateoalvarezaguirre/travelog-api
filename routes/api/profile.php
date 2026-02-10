<?php

use Src\Profile\Infrastructure\Http\Controllers\GetProfileController;

Route::prefix('auth')->group(function (): void {
    Route::middleware('auth:sanctum')->get('/me', GetProfileController::class);
});
