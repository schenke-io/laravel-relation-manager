<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Console;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Console\RelationshipBuilder;
use SchenkeIo\LaravelRelationManager\Define\Project;

class RelationshipBuilderTest extends TestCase
{
    use RelationshipBuilder;

    protected string $signature = '';

    public function testProjectIncludes()
    {

        $command = new class() extends DummyCommand
        {
            use RelationshipBuilder;

            public Project $project;

            protected $signature = 'make:it';

            public function __construct()
            {
                parent::__construct();
                $this->project = $this->projectIncludes([]);
            }
        };
        $this->assertInstanceOf(Project::class, $command->project);
    }
}
