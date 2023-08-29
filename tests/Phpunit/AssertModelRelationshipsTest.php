<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Phpunit;

use SchenkeIo\LaravelRelationManager\Demo\Models\Capital;
use SchenkeIo\LaravelRelationManager\Demo\Models\City;
use SchenkeIo\LaravelRelationManager\Demo\Models\Country;
use SchenkeIo\LaravelRelationManager\Demo\Models\HighWay;
use SchenkeIo\LaravelRelationManager\Demo\Models\Region;
use SchenkeIo\LaravelRelationManager\Demo\Models\Single;
use SchenkeIo\LaravelRelationManager\Phpunit\AssertModelRelationships;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

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
        $this->assertModelBelongsToMany(City::class, HighWay::class);
    }

    public function testModelIsSingle()
    {
        $this->assertModelIsSingle(Single::class);
    }
}
