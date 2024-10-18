<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Data;

use SchenkeIo\LaravelRelationManager\Data\ModelRelationData;
use SchenkeIo\LaravelRelationManager\Enums\Relations;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use Workbench\App\Models\Country;

class ModelRelationDataTest extends TestCase
{
    public function testCanCreateModelRelation()
    {
        $modelRelation = new ModelRelationData(
            Country::class,
            Country::class,
            Relations::noRelation,
            true
        );
        $this->assertTrue($modelRelation->preventInverse);
    }
}
