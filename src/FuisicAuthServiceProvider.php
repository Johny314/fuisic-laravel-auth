<?php

namespace Fuisic\Auth;

use Fuisic\Auth\Listeners\SocialiteWasCalledListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class FuisicAuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/fuisic-auth.php', 'fuisic-auth');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'fuisic-auth');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/fuisic-auth.php' => config_path('fuisic-auth.php'),
            ], 'fuisic-auth-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'fuisic-auth-migrations');
        }

        Event::listen(SocialiteWasCalled::class, SocialiteWasCalledListener::class);

        $prefix = config('fuisic-auth.route_prefix');

        Route::group([
            'prefix' => $prefix !== '' ? $prefix : null,
            'middleware' => config('fuisic-auth.middleware'),
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/auth.php');
        });
    }
}
