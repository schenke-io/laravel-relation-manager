<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Define;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Enums\Relation;

class RelationsTest extends TestCase
{
    public function test_all_relationship_functions()
    {
        foreach (Relation::cases() as $case) {
            $this->assertIsString($case->getAssertName());
            $this->assertIsBool($case->hasPublicFunction());
            $this->assertIsBool($case->askForInverse());
            $this->assertInstanceOf(Relation::class, $case->inverse(true));
            $this->assertInstanceOf(Relation::class, $case->inverse(false));
        }
    }

    public function test_get_class()
    {
        $this->assertIsString(Relation::hasOne->getClass());
    }
}
