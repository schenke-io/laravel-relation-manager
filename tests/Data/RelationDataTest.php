<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Data;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Data\RelationData;
use SchenkeIo\LaravelRelationManager\Define\RelationshipEnum;
use SchenkeIo\LaravelRelationManager\Demo\Models\Capital;
use SchenkeIo\LaravelRelationManager\Demo\Models\Country;

class RelationDataTest extends TestCase
{
    public static function dataProviderRelationData(): array
    {
        return [
            'two empty models' => ['', '', RelationshipEnum::noRelation, ' is not a valid model'],
            'one empty model 1' => [Country::class, '', RelationshipEnum::noRelation, ' is not a valid model'],
            'one empty model 2' => ['', Country::class, RelationshipEnum::noRelation, ' is not a valid model'],
            'two correct models but no line' => [Capital::class, Country::class, RelationshipEnum::belongsTo, ''],
            'two correct models with line' => [Country::class, Capital::class, RelationshipEnum::hasOne, "capitals ---> countries\n"],
        ];
    }

    /**
     * @dataProvider dataProviderRelationData
     *
     * @return void
     */
    public function testMermaidLine(string $model1, string $model2, RelationshipEnum $relationship, string $result)
    {
        $relationData = new RelationData($model1, $model2, $relationship);
        $this->assertEquals($result, $relationData->getMermaidLine());

    }
}
