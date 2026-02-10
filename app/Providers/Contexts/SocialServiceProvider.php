<?php

declare(strict_types=1);

namespace App\Providers\Contexts;

use App\Models\Like;
use App\Models\TripComment;
use Illuminate\Support\ServiceProvider;
use Src\Social\Application\UseCases\AddCommentUseCase;
use Src\Social\Application\UseCases\FollowUserUseCase;
use Src\Social\Application\UseCases\LikeTripUseCase;
use Src\Social\Application\UseCases\ListCommentsUseCase;
use Src\Social\Application\UseCases\UnfollowUserUseCase;
use Src\Social\Application\UseCases\UnlikeTripUseCase;
use Src\Social\Domain\Repositories\CommentRepository;
use Src\Social\Domain\Repositories\FollowRepository;
use Src\Social\Domain\Repositories\LikeRepository;
use Src\Social\Infrastructure\Database\Repositories\CommentEloquentRepository;
use Src\Social\Infrastructure\Database\Repositories\FollowEloquentRepository;
use Src\Social\Infrastructure\Database\Repositories\LikeEloquentRepository;
use Src\Social\Infrastructure\Observers\LikeObserver;
use Src\Social\Infrastructure\Observers\TripCommentObserver;

class SocialServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CommentRepository::class, CommentEloquentRepository::class);
        $this->app->bind(LikeRepository::class, LikeEloquentRepository::class);
        $this->app->bind(FollowRepository::class, FollowEloquentRepository::class);

        $this->app->bind(ListCommentsUseCase::class, fn ($app) => new ListCommentsUseCase(
            $app->make(CommentRepository::class),
        ));
        $this->app->bind(AddCommentUseCase::class, fn ($app) => new AddCommentUseCase(
            $app->make(CommentRepository::class),
        ));
        $this->app->bind(LikeTripUseCase::class, fn ($app) => new LikeTripUseCase(
            $app->make(LikeRepository::class),
        ));
        $this->app->bind(UnlikeTripUseCase::class, fn ($app) => new UnlikeTripUseCase(
            $app->make(LikeRepository::class),
        ));
        $this->app->bind(FollowUserUseCase::class, fn ($app) => new FollowUserUseCase(
            $app->make(FollowRepository::class),
        ));
        $this->app->bind(UnfollowUserUseCase::class, fn ($app) => new UnfollowUserUseCase(
            $app->make(FollowRepository::class),
        ));
    }

    public function boot(): void
    {
        Like::observe(LikeObserver::class);
        TripComment::observe(TripCommentObserver::class);
    }
}
