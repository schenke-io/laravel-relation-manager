<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Define;

use SchenkeIo\LaravelRelationManager\Define\ProjectContainer;
use SchenkeIo\LaravelRelationManager\Define\RelationsEnum;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use Workbench\App\Models\Capital;
use Workbench\App\Models\Country;

class ProjectContainerTest extends TestCase
{
    public function testClear()
    {
        ProjectContainer::addError('123');
        ProjectContainer::addModel(Country::class);
        ProjectContainer::clear();
        $this->assertCount(0, ProjectContainer::getErrors());
        $this->assertCount(0, ProjectContainer::getRelations());
    }

    public function testSetModelNameSpace()
    {
        ProjectContainer::clear();
        ProjectContainer::setModelNameSpace('Workbench\App\Models');
        $this->assertEquals('Workbench\App\Models\Country', ProjectContainer::getModelClass('Country', ''));
    }

    public function testAddModel()
    {
        ProjectContainer::clear();
        $this->assertCount(0, ProjectContainer::getRelations());
        ProjectContainer::addModel(Country::class);
        $this->assertCount(1, ProjectContainer::getRelations());
    }

    public function testAddRelation()
    {
        ProjectContainer::clear();
        $this->assertCount(0, ProjectContainer::getRelations());
        // add good relation
        ProjectContainer::addRelation(Country::class, Capital::class, RelationsEnum::hasOne);
        $this->assertCount(1, ProjectContainer::getRelations());
        $this->assertCount(0, ProjectContainer::getErrors());
        // add another different relation
        ProjectContainer::addRelation(Country::class, Capital::class, RelationsEnum::hasMany);
        $this->assertCount(1, ProjectContainer::getRelations());
        $this->assertCount(1, ProjectContainer::getErrors());
    }

    public function testAddError()
    {
        ProjectContainer::clear();
        $this->assertCount(0, ProjectContainer::getErrors());
        ProjectContainer::addError('123');
        $this->assertCount(1, ProjectContainer::getErrors());
    }

    public static function dataProviderModels()
    {
        return [
            'real model' => [Country::class, 'Workbench\App\Models\Country', 0],
            'no class' => ['xxx', '', 1],
            'wrong class' => [ProjectContainer::class, '', 1],
        ];
    }

    /**
     * @dataProvider dataProviderModels
     *
     * @return void
     */
    public function testGetModelClass(string $modelClass, string $result, int $errorCount)
    {
        ProjectContainer::clear();
        $this->assertEquals($result, ProjectContainer::getModelClass($modelClass, ''));
        $this->assertCount($errorCount, ProjectContainer::getErrors());
    }

    public function testGetRelationTable()
    {
        ProjectContainer::clear();
        ProjectContainer::addRelation(Country::class, Capital::class, RelationsEnum::hasOne);
        $this->assertCount(1, ProjectContainer::getRelationTable());
        $this->assertGreaterThanOrEqual(5, strlen(ProjectContainer::getMarkdownRelationTable()));
        $this->assertGreaterThanOrEqual(5, strlen(ProjectContainer::getMermaidCode()));
    }
}
