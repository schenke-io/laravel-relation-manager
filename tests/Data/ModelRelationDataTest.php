<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Data;

use SchenkeIo\LaravelRelationManager\Data\ModelRelationData;
use SchenkeIo\LaravelRelationManager\Define\Project;
use SchenkeIo\LaravelRelationManager\Define\RelationshipEnum;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class ModelRelationDataTest extends TestCase
{
    public function testCanCreateModelRelation()
    {
        $modelRelation = new ModelRelationData(
            Project::class,
            Project::class,
            RelationshipEnum::noRelation,
            true
        );
        $this->assertTrue($modelRelation->noInverse);
    }
}
