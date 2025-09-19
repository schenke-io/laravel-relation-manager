<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Define;

use SchenkeIo\LaravelRelationManager\Enums\Relation;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use Workbench\App\Models\City;
use Workbench\App\Models\Country;

class RelationTypeTest extends testCase
{
    public function test_get_assert_name()
    {
        $this->assertEquals('assertModelHasOne', Relation::hasOne->getAssertName());
    }

    public function test_set_table_links()
    {
        $tables = [];
        foreach (Relation::cases() as $case) {
            $case->setTableLinks(Country::class, City::class, $tables, true);
        }
        $this->assertCount(3, $tables);

        $tables = [];
        foreach (Relation::cases() as $case) {
            $case->setTableLinks(Country::class, City::class, $tables, false);
        }
        $this->assertCount(2, $tables);

    }

    public function test_inverse_methods()
    {
        $askForInverseCountNormal = 0;
        $askForInverseCountPrevent = 0;
        $hasInverseNormal = 0;
        $hasInversePrevent = 0;
        $hasPublicFunction = 0;
        $isRelation = 0;
        $askForRelatedModel = 0;

        foreach (Relation::cases() as $case) {
            $askForInverseCountNormal += ($case->inverse(false)->askForInverse() ? 1 : 0);
            $askForInverseCountPrevent += ($case->inverse(true)->askForInverse() ? 1 : 0);
            $hasInverseNormal += ($case->hasInverse(false) ? 1 : 0);
            $hasInversePrevent += ($case->hasInverse(true) ? 1 : 0);
            $hasPublicFunction += ($case->hasPublicFunction() ? 1 : 0);
            $isRelation += ($case->isRelation() ? 1 : 0);
            $askForRelatedModel += ($case->askForRelatedModel() ? 1 : 0);
        }
        $this->assertEquals(1, $askForInverseCountNormal);
        $this->assertEquals(0, $askForInverseCountPrevent);
        $this->assertEquals(6, $hasInverseNormal);
        $this->assertEquals(0, $hasInversePrevent);
        $this->assertEquals(10, $hasPublicFunction);
        $this->assertEquals(12, $isRelation);
        $this->assertEquals(10, $askForRelatedModel);
    }

    /**
     * @throws \Exception
     */
    public function test_get_class()
    {
        foreach (Relation::cases() as $case) {
            if ($case == Relation::noRelation) {
                continue;
            }
            if ($case == Relation::isSingle) {
                continue;
            }
            $class = $case->getClass();
            if (is_null($class)) {
                continue;
            }
            $this->assertStringContainsString('Illuminate\Database\Eloquent\Relations', $class);
        }
    }
}
