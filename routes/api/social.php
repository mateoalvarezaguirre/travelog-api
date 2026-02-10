<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Social\Infrastructure\Http\Controllers\AddCommentController;
use Src\Social\Infrastructure\Http\Controllers\FollowUserController;
use Src\Social\Infrastructure\Http\Controllers\LikeTripController;
use Src\Social\Infrastructure\Http\Controllers\ListCommentsController;
use Src\Social\Infrastructure\Http\Controllers\UnfollowUserController;
use Src\Social\Infrastructure\Http\Controllers\UnlikeTripController;

Route::get('/journals/{id}/comments', ListCommentsController::class);

Route::middleware(['auth:sanctum', 'throttle:30,1'])->group(function (): void {
    Route::post('/journals/{id}/like', LikeTripController::class);
    Route::post('/journals/{id}/unlike', UnlikeTripController::class);
    Route::post('/journals/{id}/comments', AddCommentController::class);

    Route::post('/users/{id}/follow', FollowUserController::class);
    Route::post('/users/{id}/unfollow', UnfollowUserController::class);
});
