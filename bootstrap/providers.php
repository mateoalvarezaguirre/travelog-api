<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,

    // Context Providers
    App\Providers\Contexts\AuthServiceProvider::class,
    App\Providers\Contexts\ProfileServiceProvider::class,
    App\Providers\Contexts\TripServiceProvider::class,
    App\Providers\Contexts\SocialServiceProvider::class,
    App\Providers\Contexts\PlaceServiceProvider::class,
    App\Providers\Contexts\SearchServiceProvider::class,
];
