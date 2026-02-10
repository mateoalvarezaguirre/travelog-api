<?php

declare(strict_types=1);

use Src\Auth\Infrastructure\Http\Controllers\ForgotPasswordController;
use Src\Auth\Infrastructure\Http\Controllers\GetMeController;
use Src\Auth\Infrastructure\Http\Controllers\GoogleAuthController;
use Src\Auth\Infrastructure\Http\Controllers\LoginController;
use Src\Auth\Infrastructure\Http\Controllers\LogoutController;
use Src\Auth\Infrastructure\Http\Controllers\RegisterController;
use Src\Auth\Infrastructure\Http\Controllers\ResetPasswordController;

Route::prefix('auth')->middleware('throttle:5,1')->group(function (): void {
    Route::post('/login', LoginController::class);
    Route::post('/register', RegisterController::class);
    Route::post('/google', GoogleAuthController::class);
    Route::post('/forgot-password', ForgotPasswordController::class);
    Route::post('/reset-password', ResetPasswordController::class);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/me', GetMeController::class);
        Route::post('/logout', LogoutController::class);
    });
});
