<?php

namespace SchenkeIo\LaravelRelationManager\Define;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Process;
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
        protected Command $command = new Command()
    ) {

    }

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
        $testCommand = config(ProjectContainer::CONFIG_KEY_TEST_COMMAND);
        $command = "$testCommand --group=".GenerateProjectTestFile::testGroup();
        $this->command->info("running command:  $command");
        echo Process::run($command)->output();

        return $this;
    }

    public function showTables(): self
    {
        $this->command->table(['model', 'related models'], ProjectContainer::getRelationTable());
        $this->command->table(['table', 'expected columns'], ProjectContainer::getDatabaseTable());

        return $this;
    }

    public function writeMarkdown(): self
    {
        $result = $this->generateMarkdownFile->writeFile(command: $this->command);
        if (! is_null($result)) {
            $this->command->error($result);

            return $this;
        }
        $this->command->info('markdown file written');

        return $this;
    }
}
