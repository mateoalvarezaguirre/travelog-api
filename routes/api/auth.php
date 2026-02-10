<?php

use Src\Auth\Infrastructure\Http\Controllers\AuthController;
use Src\Auth\Infrastructure\Http\Controllers\GoogleAuthController;
use Src\Auth\Infrastructure\Http\Controllers\LoginController;
use Src\Auth\Infrastructure\Http\Controllers\RegisterController;

Route::prefix('auth')->group(function (): void {
    Route::post('/login', LoginController::class);
    Route::post('/register', RegisterController::class);
    Route::post('/google', GoogleAuthController::class);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

    Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
});
