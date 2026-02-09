<?php

use Src\Auth\Infrastructure\Http\Controllers\AuthController;
use Src\Auth\Infrastructure\Http\Controllers\LoginController;

Route::prefix('auth')->group(function (): void {
    Route::post('/login', LoginController::class);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/google', [AuthController::class, 'google']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

    Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
});
