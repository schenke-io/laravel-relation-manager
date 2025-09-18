<?php

namespace SchenkeIo\LaravelRelationManager\Define;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Process;
use SchenkeIo\LaravelRelationManager\Enums\ConfigKey;
use SchenkeIo\LaravelRelationManager\Enums\DiagramDirection;
use SchenkeIo\LaravelRelationManager\Scanner\RelationReader;
use SchenkeIo\LaravelRelationManager\Writer\GenerateMarkdownFile;
use SchenkeIo\LaravelRelationManager\Writer\GenerateProjectTestFile;

class RelationManager
{
    /**
     * @mixin DefineRelation
     */
    public function __construct(
        protected Filesystem $fileSystem = new Filesystem,
        protected GenerateProjectTestFile $generateProjectTestFile = new GenerateProjectTestFile,
        protected GenerateMarkdownFile $generateMarkdownFile = new GenerateMarkdownFile,
        protected Command $command = new Command
    ) {}

    public function model(string $modelName): DefineRelation
    {
        return new DefineRelation($modelName);
    }

    public function writeTest(bool $strict): self
    {
        $hadErrors = false;
        foreach (ProjectContainer::getErrors() as $errorMsg) {
            $hadErrors = true;
            $this->command->warn($errorMsg);
        }
        if ($hadErrors) {
            $this->command->error('errors found, writing aborted');

            return $this;
        }

        $result = $this->generateProjectTestFile->writeFile($this->command, $strict);
        if (! is_null($result)) {
            $this->command->error($result);

            return $this;
        }
        $this->command->info('test file written');

        return $this;
    }

    public function runTest(): self
    {
        $testCommand = ConfigKey::TEST_COMMAND->get();
        $command = "$testCommand --group=".GenerateProjectTestFile::testGroup();
        $this->command->info("running command:  $command");
        echo Process::run($command)->output();

        return $this;
    }

    public function showTables(): self
    {
        $this->command->table(...ProjectContainer::getRelationTable());
        $this->command->table(...ProjectContainer::getDatabaseTable());

        return $this;
    }

    /**
     * @param  bool  $diagrammDirectionTd  either true = top-down or false = right-left
     * @return $this
     */
    public function writeMarkdown(bool $diagrammDirectionTd = true): self
    {
        ProjectContainer::$diagrammDirection = DiagramDirection::fromBool($diagrammDirectionTd);
        $result = $this->generateMarkdownFile->writeFile(command: $this->command);
        if (! is_null($result)) {
            $this->command->error($result);

            return $this;
        }
        $this->command->info('markdown file written');

        return $this;
    }

    public function scanRelations(): self
    {
        $this->command->info('scanning existing relations');
        $relationReader = new RelationReader;
        $this->command->info($relationReader->displayRelations());

        $this->command->info('');

        return $this;
    }
}
