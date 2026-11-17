<?php

namespace ItmoSoft\Localization;

use Illuminate\Support\ServiceProvider;

class LocalizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/localization.php', 'localization');

        $this->app->singleton(LocaleManager::class, fn () => new LocaleManager($this->app));
        $this->app->alias(LocaleManager::class, 'localization');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/localization.php' => config_path('localization.php'),
        ], 'itmosoft-localization-config');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
