<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Define;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Process;
use Mockery;
use Mockery\MockInterface;
use SchenkeIo\LaravelRelationManager\Define\DefineRelation;
use SchenkeIo\LaravelRelationManager\Define\ProjectContainer;
use SchenkeIo\LaravelRelationManager\Define\RelationManager;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use SchenkeIo\LaravelRelationManager\Writer\GenerateMarkdownFile;
use SchenkeIo\LaravelRelationManager\Writer\GenerateProjectTestFile;

class RelationManagerTest extends TestCase
{
    public function test_write_relation_success()
    {
        ProjectContainer::clear();
        $mockGenerateProjectTestFile = Mockery::mock(GenerateProjectTestFile::class);

        $mockCommand = Mockery::mock(Command::class);
        $mockCommand->shouldReceive('info')->times(3);

        $handler = new RelationManager(
            generateProjectTestFile: $mockGenerateProjectTestFile,
            command: $mockCommand
        );
        $handler->scanRelations();
    }

    public function test_write_test_errors()
    {
        /** @var Filesystem $mockFilesystem */
        $mockFilesystem = $this->mock(Filesystem::class, function (MockInterface $mock) {
            $mock->expects('put')->zeroOrMoreTimes();
        });
        /** @var Command $mockCommand */
        $mockCommand = $this->mock(Command::class, function (MockInterface $mock) {
            $mock->expects('warn')->times(3);
            $mock->expects('error')->times(1);
        });
        ProjectContainer::clear();
        ProjectContainer::addError('1');
        ProjectContainer::addError('1');
        ProjectContainer::addError('1');

        $handler = new RelationManager(
            fileSystem: $mockFilesystem,
            command: $mockCommand
        );
        $handler->writeTest('', '', true);
    }

    public function test_write_test_success()
    {
        ProjectContainer::clear();
        $mockGenerateProjectTestFile = Mockery::mock(GenerateProjectTestFile::class);
        $mockGenerateProjectTestFile->shouldReceive('writeFile')->once()->andReturn(null);

        $mockCommand = Mockery::mock(Command::class);
        $mockCommand->shouldReceive('info')->once();

        $handler = new RelationManager(
            generateProjectTestFile: $mockGenerateProjectTestFile,
            command: $mockCommand
        );
        $handler->writeTest(false);
    }

    public function test_write_test_return_error()
    {
        ProjectContainer::clear();
        $mockGenerateProjectTestFile = Mockery::mock(GenerateProjectTestFile::class);
        $mockGenerateProjectTestFile->shouldReceive('writeFile')->once()->andReturn('error');

        $mockCommand = Mockery::mock(Command::class);
        $mockCommand->shouldReceive('error')->once();

        $handler = new RelationManager(
            generateProjectTestFile: $mockGenerateProjectTestFile,
            command: $mockCommand
        );

        $handler->writeTest('', '', false);
    }

    public function test_run_test_success()
    {
        ProjectContainer::clear();

        $mockCommand = Mockery::mock(Command::class);
        $mockCommand->shouldReceive('info')->once();

        Process::fake(['*' => Process::result(exitCode: 0)]);

        $handler = new RelationManager(command: $mockCommand);
        $handler->runTest();
    }

    public function test_run_test_failure()
    {
        ProjectContainer::clear();

        $mockCommand = Mockery::mock(Command::class);
        $mockCommand->shouldReceive('info')->once();

        Process::fake(['*' => Process::result(exitCode: 1)]);

        $handler = new RelationManager(command: $mockCommand);
        $handler->runTest();
    }

    public function test_model()
    {
        $handler = new RelationManager;
        $this->assertInstanceOf(DefineRelation::class, $handler->model(''));
    }

    public function test_show_model_table()
    {
        $mockCommand = Mockery::mock(Command::class);
        $mockCommand->shouldReceive('table')->twice();
        $handler = new RelationManager(command: $mockCommand);
        $handler->showTables();
    }

    public function test_write_markdown_success()
    {
        $mockGenerateMarkdownFile = Mockery::mock(GenerateMarkdownFile::class);
        $mockGenerateMarkdownFile->expects('writeFile')->once()->andReturn(null);

        $mockCommand = Mockery::mock(Command::class);
        $mockCommand->shouldReceive('info')->once();

        $handler = new RelationManager(
            generateMarkdownFile: $mockGenerateMarkdownFile,
            command: $mockCommand
        );
        $handler->writeMarkdown('');
    }

    public function test_write_markdown_failure()
    {
        $mockGenerateMarkdownFile = Mockery::mock(GenerateMarkdownFile::class);
        $mockGenerateMarkdownFile->expects('writeFile')->once()->andReturn('error');

        $mockCommand = Mockery::mock(Command::class);
        $mockCommand->shouldReceive('error')->once();

        $handler = new RelationManager(
            generateMarkdownFile: $mockGenerateMarkdownFile,
            command: $mockCommand
        );
        $handler->writeMarkdown('');
    }
}
