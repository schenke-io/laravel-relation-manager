<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Console;

use SchenkeIo\LaravelRelationManager\Console\RelationManagerCommand;
use SchenkeIo\LaravelRelationManager\Define\RelationManager;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class RelationManagerCommandTest extends TestCase
{
    public function testExtendsBaseClass()
    {
        $command = new class extends RelationManagerCommand
        {
            public function getRelationManager(): RelationManager
            {
                return $this->relationManager;
            }
        };
        $this->assertInstanceOf(RelationManager::class, $command->getRelationManager());
    }
}
