<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Define;

use SchenkeIo\LaravelRelationManager\Define\DefineRelation;
use SchenkeIo\LaravelRelationManager\Define\ProjectContainer;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use SchenkeIo\LaravelRelationManager\Writer\GetDiagramm;
use Workbench\App\Console\Commands\RunTestProjectManagerCommand;
use Workbench\App\Models\City;
use Workbench\App\Models\Highway;

class DefineRelationTest extends TestCase
{
    public function test_construct()
    {
        ProjectContainer::clear();
        $defineRelation = new DefineRelation(City::class);
        $this->assertCount(1, ProjectContainer::getRelations());
        $defineRelation->isManyToMany(Highway::class, true);
        $this->assertCount(2, ProjectContainer::getRelations());

        $this->assertIsString(GetDiagramm::getMermaidCode(
            ProjectContainer::getDatabaseData(true),
            ProjectContainer::$diagrammDirection
        )
        );
        $this->assertIsString(GetDiagramm::getMermaidCode(
            ProjectContainer::getDatabaseData(false),
            ProjectContainer::$diagrammDirection
        )
        );
    }

    public function test_build_relation()
    {
        ProjectContainer::clear();

        $testCommand = new class extends RunTestProjectManagerCommand {};
        $testCommand->buildRelations();
        $this->assertCount(7, ProjectContainer::getRelations());

    }
}
