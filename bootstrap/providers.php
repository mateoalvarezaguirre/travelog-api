<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,

    // Context Providers
    App\Providers\Contexts\AuthServiceProvider::class,
    App\Providers\Contexts\ProfileServiceProvider::class,
];
