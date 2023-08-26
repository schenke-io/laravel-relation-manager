<?php

namespace SchenkeIo\LaravelRelationshipManager\Tests\Define;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationshipManager\Define\RelationshipEnum;

class RelationshipEnumTest extends TestCase
{
    public function testAllRelationshipFunctions()
    {
        foreach (RelationshipEnum::cases() as $case) {
            $this->assertIsString($case->getAssertName());
            $this->assertIsBool($case->hasPublicFunction());
            $this->assertIsBool($case->hasAssertFunction());
            $this->assertIsBool($case->askForModel());
            $this->assertIsBool($case->askForInverse());
            $this->assertInstanceOf(RelationshipEnum::class, $case->getInverse(true));
            $this->assertInstanceOf(RelationshipEnum::class, $case->getInverse(false));
        }
    }

    public function testGetClass()
    {
        $this->assertIsString(RelationshipEnum::hasOne->getClass());
        $this->expectException('Exception');
        RelationshipEnum::noRelation->getClass();
    }
}
