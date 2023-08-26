<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Data;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Data\RelationData;
use SchenkeIo\LaravelRelationManager\Tests\database\Models\Capital;
use SchenkeIo\LaravelRelationManager\Tests\database\Models\Country;

class RelationDataTest extends TestCase
{
    public static function dataProviderRelationData(): array
    {
        return [
            'two empty models' => ['', '', '', 'is not a valid model'],
            'one empty model 1' => [Country::class, '', '', 'is not a valid model'],
            'one empty model 2' => ['', Country::class, '', 'is not a valid model'],
            'two correct models' => [Capital::class, Country::class, '', '----'],
        ];
    }

    /**
     * @dataProvider dataProviderRelationData
     *
     * @return void
     */
    public function testMermaidLine(string $model1, string $model2, string $relationship, string $result)
    {
        $relationData = new RelationData($model1, $model2, $relationship);
        $this->assertStringContainsString($result, $relationData->getMermaidLine());

    }
}
