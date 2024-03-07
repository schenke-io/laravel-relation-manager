<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Define;

use SchenkeIo\LaravelRelationManager\Define\DefineRelation;
use SchenkeIo\LaravelRelationManager\Define\ProjectContainer;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use Workbench\App\Models\Capital;
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
        $defineRelation = new DefineRelation(Country::class);
        $defineRelation->hasOne(Capital::class, true);
        $this->assertCount(2, ProjectContainer::getRelations());
    }
}
