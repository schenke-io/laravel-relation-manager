<?php

namespace Workbench\App\Console\Commands;

use Illuminate\Console\Command;
use SchenkeIo\LaravelRelationManager\Facades\Relations;
use Workbench\App\Models\Country;
use Workbench\App\Models\Region;

//use Illuminate\Console\Command;

/**
 * this command works without Laravel being loaded
 */
class RunTestProjectCommand extends Command
{
    protected $signature = 'run:test-project';

    public const SUCCESS = 1;

    public function handle(): void
    {

        Relations::config(
            command: $this,
            modelNameSpace: 'Workbench\App\Models'
        );
        Relations::model('Capital');

        Relations::model('City')
            ->isManyToMany('Highway', true)
            ->hasOneThrough('Country');

        Relations::model(Country::class)
            ->hasOne('Capital', true)
            ->hasMany('Region', true)
            ->hasManyThrough('City');

        Relations::model('Highway');

        Relations::model(Region::class)
            ->hasMany('City', true)
            ->hasOneThrough('Capital');

        Relations::model('Single');

        Relations::writeTest(
            testClassName: 'SchenkeIo\LaravelRelationManager\Tests\Application\TestProjectTest',
            extendedTestClass: 'SchenkeIo\LaravelRelationManager\Tests\TestCase',
            strict: true
        )
            ->runTest('vendor/bin/pest')
            ->writeMarkdown(__DIR__.'/../../../docs/relations.md')
            ->showModelTable();

    }
}
