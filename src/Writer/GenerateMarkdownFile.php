<?php

namespace SchenkeIo\LaravelRelationManager\Writer;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use SchenkeIo\LaravelRelationManager\Define\ProjectContainer;

class GenerateMarkdownFile
{
    public function __construct(protected Filesystem $fileSystem = new Filesystem) {}

    public function writeFile(Command $command): ?string
    {
        $markdownFile = config(ProjectContainer::CONFIG_KEY_MARKDOWN_FILE, '');
        if ($markdownFile == '') {
            return "markdown file not found: $markdownFile";
        }

        $diagrammCode = ProjectContainer::getDiagrammCode();
        $tableRelations = ProjectContainer::getMarkdownRelationTable();
        $tableDatabase = ProjectContainer::getMarkdownDatabaseTable();
        $commandClass = get_class($command);
        $signature = $command->getName();

        $markdown = <<<markdown
<!--
written with: php artisan $signature
by Console Command $commandClass
do not manually edit this file as it will be overwritten

-->

## Model relations

$tableRelations

## Table relations

$diagrammCode

## Database

$tableDatabase

markdown;
        try {
            $this->fileSystem->put($markdownFile, $markdown);

            return null;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
