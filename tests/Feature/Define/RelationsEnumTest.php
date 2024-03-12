<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Define;

use SchenkeIo\LaravelRelationManager\Define\RelationsEnum;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use Workbench\App\Models\City;
use Workbench\App\Models\Country;

class RelationsEnumTest extends testCase
{
    public function testGetAssertName()
    {
        $this->assertEquals('assertModelHasOne', RelationsEnum::hasOne->getAssertName());
    }

    public function testSetTableLinks()
    {
        $tables = [];
        foreach (RelationsEnum::cases() as $case) {
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

        foreach (RelationsEnum::cases() as $case) {
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
        $this->assertEquals(8, $hasPublicFunction);
        $this->assertEquals(12, $isRelation);
        $this->assertEquals(8, $askForRelatedModel);
    }

    /**
     * @throws \Exception
     */
    public function testGetClass()
    {
        foreach (RelationsEnum::cases() as $case) {
            if ($case == RelationsEnum::noRelation && $case) {
                continue;
            }
            if ($case == RelationsEnum::isSingle && $case) {
                continue;
            }
            if ($case == RelationsEnum::castEnum) {
                $this->assertStringContainsString('BackedEnum', $case->getClass());
            } elseif ($case != RelationsEnum::castEnumReverse) {
                $this->assertStringContainsString('Illuminate\Database\Eloquent\Relations', $case->getClass());
            }
        }
    }
}
