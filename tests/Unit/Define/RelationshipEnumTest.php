<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Define;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Define\RelationsEnum;

class RelationshipEnumTest extends TestCase
{
    public function testAllRelationshipFunctions()
    {
        foreach (RelationsEnum::cases() as $case) {
            $this->assertIsString($case->getAssertName());
            $this->assertIsBool($case->hasPublicFunction());
            $this->assertIsBool($case->askForInverse());
            $this->assertInstanceOf(RelationsEnum::class, $case->inverse(true));
            $this->assertInstanceOf(RelationsEnum::class, $case->inverse(false));
        }
    }

    public function testGetClass()
    {
        $this->assertIsString(RelationsEnum::hasOne->getClass());
        $this->expectException('Exception');
        RelationsEnum::noRelation->getClass();
    }
}
