<?php

namespace SchenkeIo\LaravelRelationManager\Tests;

use Illuminate\Contracts\Config\Repository;
use Orchestra\Testbench\TestCase as Orchestra;
use SchenkeIo\LaravelRelationManager\Tests\Application\TestProjectTest;

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
            $config->set('relation-manager.modelNameSpace', 'Workbench\App\Models');
            $config->set('relation-manager.modelDirectory', workbench_path('app/Models'));
            $config->set('relation-manager.projectTestClass', TestProjectTest::class);
            $config->set('relation-manager.extendedTestClass', TestCase::class);
            $config->set('relation-manager.markdownFile', workbench_path('docs/relations.md'));
            $config->set('relation-manager.testCommand', 'composer test');

        });
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(workbench_path('database/migrations'));
    }
}
