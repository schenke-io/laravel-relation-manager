<?php

namespace SchenkeIo\LaravelRelationshipManager\Tests\Data;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationshipManager\Data\ModelRelationData;
use SchenkeIo\LaravelRelationshipManager\Data\ProjectData;
use SchenkeIo\LaravelRelationshipManager\Define\RelationshipEnum;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Capital;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Country;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Single;

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
