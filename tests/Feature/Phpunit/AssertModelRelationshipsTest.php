<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Phpunit;

use SchenkeIo\LaravelRelationManager\Phpunit\AssertModelRelations;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use Workbench\App\Models\Capital;
use Workbench\App\Models\City;
use Workbench\App\Models\Country;
use Workbench\App\Models\GeoRegion;
use Workbench\App\Models\Highway;
use Workbench\App\Models\Single;

class AssertModelRelationshipsTest extends TestCase
{
    use AssertModelRelations;

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
        $this->assertModelHasMany(Country::class, GeoRegion::class);
    }

    public function testModelHasOneThrough()
    {
        $this->assertModelHasOneThrough(GeoRegion::class, Capital::class);
    }

    public function testModelHasManyThrough()
    {
        $this->assertModelHasManyThrough(Country::class, City::class);
    }

    public function testModelBelongsTo()
    {
        $this->assertModelBelongsTo(GeoRegion::class, Country::class);
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
