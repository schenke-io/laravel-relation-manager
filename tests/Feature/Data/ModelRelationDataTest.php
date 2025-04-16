<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Data;

use SchenkeIo\LaravelRelationManager\Data\ModelRelationData;
use SchenkeIo\LaravelRelationManager\Enums\Relation;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use Workbench\App\Models\Country;

class ModelRelationDataTest extends TestCase
{
    public function test_can_create_model_relation()
    {
        $modelRelation = new ModelRelationData(
            Country::class,
            Country::class,
            Relation::noRelation,
            true
        );
        $this->assertTrue($modelRelation->preventInverse);
    }
}
