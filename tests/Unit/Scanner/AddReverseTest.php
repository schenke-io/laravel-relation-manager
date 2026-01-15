<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Scanner;

use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;
use SchenkeIo\LaravelRelationManager\Scanner\ModelScanner;
use SchenkeIo\LaravelRelationManager\Tests\Models\ReverseModel;
use SchenkeIo\LaravelRelationManager\Tests\Models\User;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class AddReverseTest extends TestCase
{
    public function test_scan_handles_add_reverse()
    {
        $scanner = new ModelScanner;
        // we only scan the directory where ReverseModel is
        $results = $scanner->scan(__DIR__.'/../../Models');

        $this->assertArrayHasKey(ReverseModel::class, $results);
        $this->assertArrayHasKey(User::class, $results);

        // check if User has the injected belongsTo relation
        $this->assertArrayHasKey('reversemodel', $results[User::class]);
        $this->assertEquals(EloquentRelation::belongsTo, $results[User::class]['reversemodel']['type']);
        $this->assertEquals(ReverseModel::class, $results[User::class]['reversemodel']['related']);
    }
}
