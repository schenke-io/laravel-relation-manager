<?php

namespace SchenkeIo\LaravelRelationshipManager\Tests\Console;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationshipManager\Console\RelationshipBuilder;
use SchenkeIo\LaravelRelationshipManager\Define\Project;

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
