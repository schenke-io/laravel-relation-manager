<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Data;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Data\ModelRelationData;
use SchenkeIo\LaravelRelationManager\Data\ProjectData;
use SchenkeIo\LaravelRelationManager\Define\RelationshipEnum;
use SchenkeIo\LaravelRelationManager\Tests\database\Models\Capital;
use SchenkeIo\LaravelRelationManager\Tests\database\Models\Country;
use SchenkeIo\LaravelRelationManager\Tests\database\Models\Single;

class ProjectDataTest extends TestCase
{
    public function testGetAllModels()
    {

        $this->assertIsArray((new ProjectData())->getAllModels());
    }

    public static function dataProviderModelRelationSet(): array
    {

        return [
            // name              ErrorCount   ModelRelation
            'empty model 1' => [1,
                [
                    new ModelRelationData('', null, RelationshipEnum::noRelation, true),
                ],
            ],
            'single model' => [0,
                [
                    new ModelRelationData(Single::class, null, RelationshipEnum::isSingle, true),
                ],
            ],
            'two good models' => [0,
                [
                    new ModelRelationData(Country::class, Capital::class, RelationshipEnum::hasOne, true),
                ],
            ],
            'double relation definition' => [1,
                [
                    new ModelRelationData(Country::class, Capital::class, RelationshipEnum::hasOne, true),
                    new ModelRelationData(Country::class, Capital::class, RelationshipEnum::hasMany, true),
                ],
            ],

        ];
    }

    #[DataProvider('dataProviderModelRelationSet')]
    public function testGetErrors(int $errorCount, array $modelRelations)
    {
        $projectData = new ProjectData($modelRelations);
        $errors = $projectData->getErrors();
        $this->assertCount($errorCount, $errors, implode(', ', $errors));
    }

    public function testGetShortModelName()
    {
        $projectData = new ProjectData();
        $this->assertEquals('User', $projectData->getShortModelName('App\Models\User'));
    }
}
