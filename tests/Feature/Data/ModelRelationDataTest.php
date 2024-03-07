<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Data;

use SchenkeIo\LaravelRelationManager\Data\ModelRelationData;
use SchenkeIo\LaravelRelationManager\Define\RelationsEnum;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use Workbench\App\Models\Country;

class ModelRelationDataTest extends TestCase
{
    public function testCanCreateModelRelation()
    {
        $modelRelation = new ModelRelationData(
            Country::class,
            Country::class,
            RelationsEnum::noRelation,
            true
        );
        $this->assertTrue($modelRelation->preventInverse);
    }
}
