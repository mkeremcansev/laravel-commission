<?php

namespace Mkeremcansev\LaravelCommission;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelCommissionServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-commission')
            ->hasConfigFile()
            ->hasMigrations();
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'laravel-commission-migrations');

        $this->publishes([
            __DIR__.'/../config/commission.php' => config_path('commission.php'),
        ], 'laravel-commission-config');
    }
}
