<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Data;

use Illuminate\Foundation\Testing\RefreshDatabase;
use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Define\RelationshipEnum;
use SchenkeIo\LaravelRelationManager\Demo\Models\Capital;
use SchenkeIo\LaravelRelationManager\Demo\Models\Country;
use SchenkeIo\LaravelRelationManager\Demo\Models\Single;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class RelationScanTest extends TestCase
{
    use RefreshDatabase;

    public function testRelationCountOfModel()
    {
        $this->assertEquals(-1, ClassData::getRelationCountOfModel(''));
        $this->assertEquals(-1, ClassData::getRelationCountOfModel(RelationScanTest::class));
        //        $this->assertEquals(1, RelationScan::getRelationCountOfModel(Capital::class));
        $this->assertEquals(0, ClassData::getRelationCountOfModel(Single::class));
    }

    public function testGetRelationNotFoundError()
    {
        $this->assertEquals('',
            ClassData::getRelationExpectation(
                Country::class,
                RelationshipEnum::hasOne,
                Capital::class
            )
        );
    }
}
