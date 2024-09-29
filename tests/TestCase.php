<?php

namespace Mkeremcansev\LaravelCommission\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mkeremcansev\LaravelCommission\LaravelCommissionServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Mkeremcansev\\LaravelCommission\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelCommissionServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-commission_table.php.stub';
        $migration->up();
        */
    }
}
