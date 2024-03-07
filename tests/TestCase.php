<?php

namespace SchenkeIo\LaravelRelationManager\Tests;

use Illuminate\Contracts\Config\Repository;
use Orchestra\Testbench\TestCase as Orchestra;

use function Orchestra\Testbench\workbench_path;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [

        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        // Setup default database to use sqlite :memory:
        tap($app->make('config'), function (Repository $config) {
            $config->set('database.default', 'testbench');
            $config->set('database.connections.testbench', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]);
        });
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(workbench_path('database/migrations'));
    }
}
