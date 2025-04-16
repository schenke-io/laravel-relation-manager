<?php

namespace Workbench\App\Console\Commands;

use SchenkeIo\LaravelRelationManager\Console\RelationManagerCommand;
use Workbench\App\Models\City;
use Workbench\App\Models\Country;
use Workbench\App\Models\Highway;
use Workbench\App\Models\Location;
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
            ->writeTest(strict: true)
            ->runTest();

    }

    public function buildRelations(): void
    {

        $this->relationManager->model(City::class)
            ->isManyToMany(Highway::class, true)
            ->morphOne('Location', true)
            ->hasOneThrough('Country');

        $this->relationManager->model(Country::class)
            ->hasOne('Capital', true)
            ->hasMany(Region::class, true)
            ->hasOneIndirect('City')
            ->hasManyThrough('City');

        $this->relationManager->model('Highway');

        $this->relationManager->model(Region::class)
            ->hasMany('City', true)
            ->hasOneThrough('Capital');

        $this->relationManager->model(Highway::class)
            ->morphMany(Location::class, true);

        $this->relationManager->model('Single');

        $this->relationManager->model('Capital')
            ->morphOne('Location', true);
    }
}
