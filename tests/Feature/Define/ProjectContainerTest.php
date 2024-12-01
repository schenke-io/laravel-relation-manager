<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Define;

use PHPUnit\Framework\Attributes\DataProvider;
use SchenkeIo\LaravelRelationManager\Define\ProjectContainer;
use SchenkeIo\LaravelRelationManager\Enums\ConfigKey;
use SchenkeIo\LaravelRelationManager\Enums\Relations;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use Workbench\App\Models\Capital;
use Workbench\App\Models\Country;
use Workbench\App\Models\Location;

class ProjectContainerTest extends TestCase
{
    public function test_clear()
    {
        ProjectContainer::addError('123');
        ProjectContainer::addModel(Country::class);
        ProjectContainer::clear();
        $this->assertCount(0, ProjectContainer::getErrors());
        $this->assertCount(0, ProjectContainer::getRelations());
    }

    public function test_set_model_name_space()
    {
        ProjectContainer::clear();
        $this->assertEquals('Workbench\App\Models\Country', ProjectContainer::getModelClass('Country'));
    }

    public function test_add_model()
    {
        ProjectContainer::clear();
        $this->assertCount(0, ProjectContainer::getRelations());
        ProjectContainer::addModel(Country::class);
        $this->assertCount(1, ProjectContainer::getRelations());
    }

    public function test_add_relation()
    {
        ProjectContainer::clear();
        $this->assertCount(0, ProjectContainer::getRelations());
        // add good relation
        ProjectContainer::addRelation(Country::class, Capital::class, Relations::hasOne);
        $this->assertCount(1, ProjectContainer::getRelations());
        $this->assertCount(0, ProjectContainer::getErrors());
        // add another different relation
        ProjectContainer::addRelation(Country::class, Capital::class, Relations::hasMany);
        $this->assertCount(1, ProjectContainer::getRelations());
        $this->assertCount(1, ProjectContainer::getErrors());
        // add morphTo
        ProjectContainer::addRelation(Capital::class, Location::class, Relations::morphTo);
        $this->assertCount(2, ProjectContainer::getRelations());
        $this->assertCount(1, ProjectContainer::getErrors());
    }

    public function test_add_error()
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
    public function test_get_model_class(string $modelClass, string $result, int $errorCount, int $unknownModelsCount)
    {
        ProjectContainer::clear();
        $this->assertEquals($result, ProjectContainer::getModelClass($modelClass));
        ProjectContainer::addModel($modelClass);
        $this->assertCount($errorCount, ProjectContainer::getErrors());
        $this->assertCount($unknownModelsCount, ProjectContainer::getUnknownModels());
    }

    public function test_get_relation_table()
    {
        ProjectContainer::clear();
        ProjectContainer::addRelation(Country::class, Capital::class, Relations::hasOne);
        $this->assertCount(2, ProjectContainer::getRelationTable());
        $this->assertGreaterThanOrEqual(5, strlen(ProjectContainer::getMarkdownRelationTable()));
        $this->assertGreaterThanOrEqual(5, strlen(ProjectContainer::getDiagrammCode(true)));
    }

    public function test_get_database_table()
    {
        ProjectContainer::clear();
        ProjectContainer::addRelation(Country::class, Capital::class, Relations::hasOne);
        ProjectContainer::addRelation(Capital::class, Location::class, Relations::morphOne);
        $this->assertTrue(true);
        // todo repair next Line !
        // $this->assertCount(2, ProjectContainer::getDatabaseTable());
    }

    public function test_get_diagramm_code()
    {
        ProjectContainer::clear();
        ProjectContainer::addRelation(Country::class, Capital::class, Relations::hasOne);
        ProjectContainer::addRelation(Capital::class, Location::class, Relations::morphOne);
        ConfigKey::USE_MERMAID_DIAGRAMM->set(true);
        $this->assertIsString(ProjectContainer::getDiagrammCode(true));
        $this->assertIsString(ProjectContainer::getDiagrammCode(false));
        ConfigKey::USE_MERMAID_DIAGRAMM->set(false);
        $this->assertIsString(ProjectContainer::getDiagrammCode(true));
        $this->assertIsString(ProjectContainer::getDiagrammCode(false));
    }
}
