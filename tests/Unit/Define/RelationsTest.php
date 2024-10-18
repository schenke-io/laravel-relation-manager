<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Define;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Enums\Relations;

class RelationsTest extends TestCase
{
    public function testAllRelationshipFunctions()
    {
        foreach (Relations::cases() as $case) {
            $this->assertIsString($case->getAssertName());
            $this->assertIsBool($case->hasPublicFunction());
            $this->assertIsBool($case->askForInverse());
            $this->assertInstanceOf(Relations::class, $case->inverse(true));
            $this->assertInstanceOf(Relations::class, $case->inverse(false));
        }
    }

    public function testGetClass()
    {
        $this->assertIsString(Relations::hasOne->getClass());
        $this->expectException('Exception');
        Relations::noRelation->getClass();
    }
}