<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Define;

use SchenkeIo\LaravelRelationManager\Enums\Relations;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use Workbench\App\Models\City;
use Workbench\App\Models\Country;

class RelationTypeTest extends testCase
{
    public function testGetAssertName()
    {
        $this->assertEquals('assertModelHasOne', Relations::hasOne->getAssertName());
    }

    public function testSetTableLinks()
    {
        $tables = [];
        foreach (Relations::cases() as $case) {
            $case->setTableLinks(Country::class, City::class, $tables);
        }
        $this->assertCount(3, $tables);
    }

    public function testInverseMethods()
    {
        $askForInverseCountNormal = 0;
        $askForInverseCountPrevent = 0;
        $hasInverseNormal = 0;
        $hasInversePrevent = 0;
        $hasPublicFunction = 0;
        $isRelation = 0;
        $askForRelatedModel = 0;

        foreach (Relations::cases() as $case) {
            $askForInverseCountNormal += ($case->inverse(false)->askForInverse() ? 1 : 0);
            $askForInverseCountPrevent += ($case->inverse(true)->askForInverse() ? 1 : 0);
            $hasInverseNormal += ($case->hasInverse(false) ? 1 : 0);
            $hasInversePrevent += ($case->hasInverse(true) ? 1 : 0);
            $hasPublicFunction += ($case->hasPublicFunction() ? 1 : 0);
            $isRelation += ($case->isRelation() ? 1 : 0);
            $askForRelatedModel += ($case->askForRelatedModel() ? 1 : 0);
        }
        $this->assertEquals(0, $askForInverseCountNormal);
        $this->assertEquals(0, $askForInverseCountPrevent);
        $this->assertEquals(5, $hasInverseNormal);
        $this->assertEquals(0, $hasInversePrevent);
        $this->assertEquals(7, $hasPublicFunction);
        $this->assertEquals(10, $isRelation);
        $this->assertEquals(7, $askForRelatedModel);
    }

    /**
     * @throws \Exception
     */
    public function testGetClass()
    {
        foreach (Relations::cases() as $case) {
            if ($case == Relations::noRelation) {
                continue;
            }
            if ($case == Relations::isSingle) {
                continue;
            }
            $this->assertStringContainsString('Illuminate\Database\Eloquent\Relations', $case->getClass());
        }
    }
}
