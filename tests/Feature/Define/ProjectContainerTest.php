<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Define;

use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\DataProvider;
use SchenkeIo\LaravelRelationManager\Define\ProjectContainer;
use SchenkeIo\LaravelRelationManager\Define\RelationsEnum;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use Workbench\App\Models\AreaSize;
use Workbench\App\Models\Capital;
use Workbench\App\Models\City;
use Workbench\App\Models\Country;
use Workbench\App\Models\Location;

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
        // add morphTo
        ProjectContainer::addRelation(Capital::class, Location::class, RelationsEnum::morphTo);
        $this->assertCount(2, ProjectContainer::getRelations());
        $this->assertCount(1, ProjectContainer::getErrors());
    }

    public function testAddError()
    {
        ProjectContainer::clear();
        $this->assertCount(0, ProjectContainer::getErrors());
        ProjectContainer::addError('123');
        $this->assertCount(1, ProjectContainer::getErrors());
    }

    public static function dataProviderModels(): array
    {
        return [
            'real model' => [Country::class, 'Workbench\App\Models\Country', 0, 0],
            'no class' => ['xxx', '', 0, 1],
            'wrong class' => [ProjectContainer::class, '', 0, 1],
        ];
    }

    #[DataProvider('dataProviderModels')]
    /**
     * @return void
     */
    public function testGetModelClass(string $modelClass, string $result, int $errorCount, int $unknownModelsCount)
    {
        ProjectContainer::clear();
        $this->assertEquals($result, ProjectContainer::getModelClass($modelClass, ''));
        ProjectContainer::addModel($modelClass);
        $this->assertCount($errorCount, ProjectContainer::getErrors());
        $this->assertCount($unknownModelsCount, ProjectContainer::getUnknownModels());
    }

    public function testGetRelationTable()
    {
        ProjectContainer::clear();
        ProjectContainer::addRelation(Country::class, Capital::class, RelationsEnum::hasOne);
        $this->assertCount(1, ProjectContainer::getRelationTable());
        $this->assertGreaterThanOrEqual(5, strlen(ProjectContainer::getMarkdownRelationTable()));
        $this->assertGreaterThanOrEqual(5, strlen(ProjectContainer::getDiagrammCode()));
    }

    public function testGetDatabaseTable()
    {
        ProjectContainer::clear();
        ProjectContainer::addRelation(Country::class, Capital::class, RelationsEnum::hasOne);
        ProjectContainer::addRelation(Capital::class, Location::class, RelationsEnum::morphOne);
        ProjectContainer::addRelation(City::class, AreaSize::class, RelationsEnum::castEnum);
        ProjectContainer::addRelation(AreaSize::class, City::class, RelationsEnum::castEnumReverse);
        $this->assertCount(4, ProjectContainer::getDatabaseTable());
    }

    public function testGetDiagrammCode()
    {
        ProjectContainer::clear();
        ProjectContainer::addRelation(Country::class, Capital::class, RelationsEnum::hasOne);
        ProjectContainer::addRelation(Capital::class, Location::class, RelationsEnum::morphOne);
        ProjectContainer::addRelation(City::class, AreaSize::class, RelationsEnum::castEnum);
        ProjectContainer::addRelation(AreaSize::class, City::class, RelationsEnum::castEnumReverse);
        Config::set(ProjectContainer::CONFIG_KEY_USE_MERMAID_DIAGRAMM, true);
        $this->assertIsString(ProjectContainer::getDiagrammCode());
        Config::set(ProjectContainer::CONFIG_KEY_USE_MERMAID_DIAGRAMM, false);
        $this->assertIsString(ProjectContainer::getDiagrammCode());
    }
}
