<?php

namespace Animelhd\AnimesWatching;

use Illuminate\Support\ServiceProvider;

class WatchingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            \dirname(__DIR__).'/config/animeswatching.php' => config_path('animeswatching.php'),
        ], 'watching-config');

        $this->publishes([
            \dirname(__DIR__).'/migrations/' => database_path('migrations'),
        ], 'watching-migrations');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            \dirname(__DIR__).'/config/animeswatching.php',
            'watching'
        );
    }
}
