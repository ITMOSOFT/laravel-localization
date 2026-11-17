<?php

namespace ItmoSoft\Localization\Tests;

use ItmoSoft\Localization\LocalizationServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [LocalizationServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        $app['config']->set('localization.supported_locales', ['en', 'ur', 'ar', 'en-GB']);
        $app['config']->set('localization.fallback_locale', 'en');
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
