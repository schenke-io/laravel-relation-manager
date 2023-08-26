<?php

namespace SchenkeIo\LaravelRelationshipManager\Tests\Data;

use SchenkeIo\LaravelRelationshipManager\Data\ModelRelationData;
use SchenkeIo\LaravelRelationshipManager\Define\Project;
use SchenkeIo\LaravelRelationshipManager\Define\RelationshipEnum;
use SchenkeIo\LaravelRelationshipManager\Tests\TestCase;

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
