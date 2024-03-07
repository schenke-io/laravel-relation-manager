<?php

namespace SchenkeIo\LaravelRelationManager\Define;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Process;
use SchenkeIo\LaravelRelationManager\Writer\GenerateMarkdownFile;
use SchenkeIo\LaravelRelationManager\Writer\GenerateProjectTestFile;

class RelationsHandler
{
    private Command $command;

    /**
     * @mixin DefineRelation
     */
    public function __construct(
        protected Filesystem $fileSystem = new Filesystem,
        protected GenerateProjectTestFile $generateProjectTestFile = new GenerateProjectTestFile,
        protected GenerateMarkdownFile $generateMarkdownFile = new GenerateMarkdownFile
    ) {
        $this->command = new Command;
    }

    public function config(Command $command, string $modelNameSpace = 'App\Models'): void
    {
        ProjectContainer::setModelNameSpace($modelNameSpace);
        $this->command = $command;
    }

    public function model(string $modelName): DefineRelation
    {
        return new DefineRelation($modelName);
    }

    public function writeTest(
        string $testClassName,
        string $extendedTestClass,
        bool $strict): self
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

        $result = $this->generateProjectTestFile->writeFile(
            relations: ProjectContainer::getRelations(),
            testProjectClass: $testClassName,
            extendedTestClass: $extendedTestClass,
            callingCommand: $this->command,
            testStrict: $strict
        );
        if (! is_null($result)) {
            $this->command->error($result);

            return $this;
        }
        $this->command->info("test file written:  $testClassName");

        return $this;
    }

    public function runTest(string $testCommand = 'php artisan test'): self
    {
        $command = "$testCommand --group=".GenerateProjectTestFile::testGroup();
        $this->command->info("running command:  $command");
        echo Process::run($command)->output();

        return $this;
    }

    public function showModelTable(): self
    {
        $this->command->table(['model', '... has relations to'], ProjectContainer::getRelationTable());

        return $this;
    }

    public function writeMarkdown(string $markdownFile): self
    {
        $result = $this->generateMarkdownFile->writeFile(
            markdownFile: $markdownFile,
            command: $this->command
        );
        if (! is_null($result)) {
            $this->command->error($result);

            return $this;
        }
        $this->command->info("markdown file written:  $markdownFile");

        return $this;
    }
}
