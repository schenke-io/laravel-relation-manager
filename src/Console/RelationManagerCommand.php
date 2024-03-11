<?php

namespace SchenkeIo\LaravelRelationManager\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use SchenkeIo\LaravelRelationManager\Define\RelationManager;
use SchenkeIo\LaravelRelationManager\Writer\GenerateMarkdownFile;
use SchenkeIo\LaravelRelationManager\Writer\GenerateProjectTestFile;

class RelationManagerCommand extends Command
{
    protected RelationManager $relationManager;

    protected $signature = 'relation-manager:run';

    protected $description = 'define and write the model relations';

    public function __construct()
    {
        parent::__construct();
        $this->relationManager = new RelationManager(
            new Filesystem(),
            new GenerateProjectTestFile(),
            new GenerateMarkdownFile(),
            $this
        );
    }
}
