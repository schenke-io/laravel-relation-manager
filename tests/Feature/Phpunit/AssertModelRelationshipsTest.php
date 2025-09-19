<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Phpunit;

use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use SchenkeIo\LaravelRelationManager\Traits\AssertModelRelations;
use Workbench\App\Models\Capital;
use Workbench\App\Models\City;
use Workbench\App\Models\Country;
use Workbench\App\Models\Highway;
use Workbench\App\Models\Region;
use Workbench\App\Models\Single;

class AssertModelRelationshipsTest extends TestCase
{
    use AssertModelRelations;

    public function test_first_class_is_older_than_second_class()
    {
        $this->assertFirstClassIsOlderThanSecondClass(Country::class, Country::class);
    }

    public function test_model_relation_count()
    {
        $this->assertModelRelationCount(Country::class, 4);
    }

    public function test_model_has_one()
    {
        $this->assertModelHasOne(Country::class, Capital::class);
    }

    public function test_model_has_many()
    {
        $this->assertModelHasMany(Country::class, Region::class);
    }

    public function test_model_has_one_through()
    {
        $this->assertModelHasOneThrough(Region::class, Capital::class);
    }

    public function test_model_has_many_through()
    {
        $this->assertModelHasManyThrough(Country::class, City::class);
    }

    public function test_model_belongs_to()
    {
        $this->assertModelBelongsTo(Region::class, Country::class);
    }

    public function test_model_belongs_to_many()
    {
        $this->assertModelBelongsToMany(City::class, Highway::class);
    }

    public function test_model_is_single()
    {
        $this->assertModelIsSingle(Single::class);
    }
}
