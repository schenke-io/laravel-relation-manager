<?php

namespace SchenkeIo\LaravelRelationshipManager\Tests\Phpunit;

use SchenkeIo\LaravelRelationshipManager\Phpunit\AssertModelRelationships;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Capital;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\City;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Country;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Highway;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Region;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Single;
use SchenkeIo\LaravelRelationshipManager\Tests\TestCase;

class AssertModelRelationshipsTest extends TestCase
{
    use AssertModelRelationships;

    public function testModelWorks()
    {
        $this->assertModelWorks(Country::class);
    }

    public function testFirstClassIsOlderThanSecondClass()
    {
        $this->assertFirstClassIsOlderThanSecondClass(Country::class, Country::class);
    }

    public function testModelRelationCount()
    {
        $this->assertModelRelationCount(Country::class, 3);
    }

    public function testModelHasOne()
    {
        $this->assertModelHasOne(Country::class, Capital::class);
    }

    public function testModelHasMany()
    {
        $this->assertModelHasMany(Country::class, Region::class);
    }

    public function testModelHasOneThrough()
    {
        $this->assertModelHasOneThrough(Region::class, Capital::class);
    }

    public function testModelHasManyThrough()
    {
        $this->assertModelHasManyThrough(Country::class, City::class);
    }

    public function testModelBelongsTo()
    {
        $this->assertModelBelongsTo(Region::class, Country::class);
    }

    public function testModelBelongsToMany()
    {
        $this->assertModelBelongsToMany(City::class, Highway::class);
    }

    public function testModelIsSingle()
    {
        $this->assertModelIsSingle(Single::class);
    }
}
