<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Phpunit;

use SchenkeIo\LaravelRelationManager\Enums\Relation;
use SchenkeIo\LaravelRelationManager\Facades\ModelScanner;
use SchenkeIo\LaravelRelationManager\Phpunit\RelationTestTrait;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class RelationTestTraitCoverageTest extends TestCase
{
    use RelationTestTrait;

    public function test_all_assertions()
    {
        ModelScanner::shouldReceive('scan')->andReturn([
            'Model1' => [
                'rel1' => [
                    'type' => Relation::hasOne,
                    'related' => 'Model2',
                ],
                'rel2' => [
                    'type' => Relation::hasMany,
                    'related' => 'Model3',
                ],
                'rel3' => [
                    'type' => Relation::belongsTo,
                    'related' => 'Model4',
                ],
                'rel4' => [
                    'type' => Relation::belongsToMany,
                    'related' => 'Model5',
                ],
            ],
        ]);

        $this->assertModelHasOne('Model1', 'Model2');
        $this->assertModelHasMany('Model1', 'Model3');
        $this->assertModelBelongsTo('Model1', 'Model4');
        $this->assertModelBelongsToMany('Model1', 'Model5');
    }
}
