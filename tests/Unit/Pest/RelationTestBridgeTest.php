<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Pest;

use SchenkeIo\LaravelRelationManager\Pest\RelationTestBridge;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class RelationTestBridgeTest extends TestCase
{
    public function test_instantiation_and_all()
    {
        if (! function_exists('test')) {
            function test($name, $closure) {}
        }
        RelationTestBridge::all();
        $this->assertTrue(true);
    }
}
