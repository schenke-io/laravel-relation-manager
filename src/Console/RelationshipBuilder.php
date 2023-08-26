<?php

namespace SchenkeIo\LaravelRelationshipManager\Console;

use Illuminate\Console\Command;
use SchenkeIo\LaravelRelationshipManager\Data\ProjectData;
use SchenkeIo\LaravelRelationshipManager\Define\Project;
use SchenkeIo\LaravelRelationshipManager\Writer\SaveFileContent;

trait RelationshipBuilder
{
    /**
     * @param  \SchenkeIo\LaravelRelationshipManager\Data\ModelRelationData[]  $modelRelations <p>list of relations started with <code>sayEach()</code></p>
     * @param  string  $modelNamespace <p>namespace of the models, default = <code>App\Models</code></p>
     * @param  bool  $strict tests fail when a model has undefined relationships
     */
    protected function projectIncludes(
        array $modelRelations,
        string $modelNamespace = "App\Models",
        bool $strict = false
    ): Project {
        /** @var Command $this */
        return new Project(
            new ProjectData(
                $modelRelations,
                $this,
                $modelNamespace,
                $strict
            ),
            new SaveFileContent()
        );
    }
}
