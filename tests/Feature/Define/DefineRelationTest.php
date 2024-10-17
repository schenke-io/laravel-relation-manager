<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Define;

use SchenkeIo\LaravelRelationManager\Define\DefineRelation;
use SchenkeIo\LaravelRelationManager\Define\ProjectContainer;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use Workbench\App\Console\Commands\RunTestProjectManagerCommand;
use Workbench\App\Models\Country;

class DefineRelationTest extends TestCase
{
    public function testConstruct()
    {
        ProjectContainer::clear();
        $defineRelation = new DefineRelation(Country::class);
        $this->assertCount(1, ProjectContainer::getRelations());
    }

    public function testBuildRelation()
    {
        ProjectContainer::clear();

        $testCommand = new class extends RunTestProjectManagerCommand {};
        $testCommand->buildRelations();
        $this->assertCount(8, ProjectContainer::getRelations());
    }
}
