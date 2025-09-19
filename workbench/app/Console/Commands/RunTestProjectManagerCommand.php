<?php

namespace Workbench\App\Console\Commands;

use SchenkeIo\LaravelRelationManager\Console\RelationManagerCommand;
use Workbench\App\Models\City;
use Workbench\App\Models\Country;
use Workbench\App\Models\Highway;
use Workbench\App\Models\Region;

/**
 * this command works without Laravel being loaded
 */
class RunTestProjectManagerCommand extends RelationManagerCommand
{
    protected $signature = 'run:test-project';

    public function handle(): void
    {
        // separate because of testing
        $this->buildRelations();

        $this->relationManager->model('Green');

        $this->relationManager
            ->writeMarkdown()
            ->showTables()
            ->scanRelations()
            ->writeTest(strict: true)
            ->runTest();
    }

    public function buildRelations(): void
    {
        $this->relationManager->model('Capital')
            ->morphOne('Location', true);

        $this->relationManager->model(City::class)
            ->belongsToMany(Highway::class, true)
            ->hasOneThrough('Country')
            ->morphToMany('Tag', true)
            ->morphOne('Location', true);

        $this->relationManager->model(Country::class)
            ->hasOne('Capital', true)
            ->hasOneIndirect('City')
            ->hasManyThrough('City')
            ->hasMany(Region::class, true);

        $this->relationManager->model('Highway')
            ->morphMany('Location', true);

        $this->relationManager->model(Region::class)
            ->morphToMany('Tag', true)
            ->hasMany('City', true)
            ->hasOneThrough('Capital');

        $this->relationManager->model('Single');

    }
}
