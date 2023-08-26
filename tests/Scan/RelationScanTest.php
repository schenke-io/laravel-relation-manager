<?php

namespace SchenkeIo\LaravelRelationshipManager\Tests\Scan;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SchenkeIo\LaravelRelationshipManager\Data\ClassData;
use SchenkeIo\LaravelRelationshipManager\Scan\RelationScan;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Capital;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Country;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Single;
use SchenkeIo\LaravelRelationshipManager\Tests\TestCase;

class RelationScanTest extends TestCase
{
    use RefreshDatabase;

    public function testRelationCountOfModel()
    {
        $this->assertEquals(-1, ClassData::getRelationCountOfModel(''));
        $this->assertEquals(-1, ClassData::getRelationCountOfModel(RelationScan::class));
        //        $this->assertEquals(1, RelationScan::getRelationCountOfModel(Capital::class));
        $this->assertEquals(0, ClassData::getRelationCountOfModel(Single::class));
    }

    public function testGetRelationNotFoundError()
    {
        $this->assertEquals('',
            ClassData::getRelationNotFoundError(
                Country::class,
                HasOne::class,
                Capital::class
            )
        );
    }
}
