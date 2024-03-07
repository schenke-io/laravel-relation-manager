<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Define;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Process;
use Mockery;
use Mockery\MockInterface;
use SchenkeIo\LaravelRelationManager\Define\DefineRelation;
use SchenkeIo\LaravelRelationManager\Define\ProjectContainer;
use SchenkeIo\LaravelRelationManager\Define\RelationsHandler;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use SchenkeIo\LaravelRelationManager\Writer\GenerateMarkdownFile;
use SchenkeIo\LaravelRelationManager\Writer\GenerateProjectTestFile;

class RelationsHandlerTest extends TestCase
{
    public function testWriteTestErrors()
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

        $handler = new RelationsHandler($mockFilesystem);
        $handler->config($mockCommand);
        $handler->writeTest('', '', true);
    }

    public function testWriteTestSuccess()
    {
        ProjectContainer::clear();
        $mockGenerateProjectTestFile = Mockery::mock(GenerateProjectTestFile::class);
        $mockGenerateProjectTestFile->shouldReceive('writeFile')->once()->andReturn(null);
        $handler = new RelationsHandler(new Filesystem(), $mockGenerateProjectTestFile);
        $mockCommand = Mockery::mock(Command::class);
        $mockCommand->shouldReceive('info')->once();
        $handler->config($mockCommand);
        $handler->writeTest('', '', false);
    }

    public function testWriteTestReturnError()
    {
        ProjectContainer::clear();
        $mockGenerateProjectTestFile = Mockery::mock(GenerateProjectTestFile::class);
        $mockGenerateProjectTestFile->shouldReceive('writeFile')->once()->andReturn('error');
        $handler = new RelationsHandler(new Filesystem(), $mockGenerateProjectTestFile);
        $mockCommand = Mockery::mock(Command::class);
        $mockCommand->shouldReceive('error')->once();
        $handler->config($mockCommand);
        $handler->writeTest('', '', false);
    }

    public function testRunTestSuccess()
    {
        ProjectContainer::clear();

        $mockCommand = Mockery::mock(Command::class);
        $mockCommand->shouldReceive('info')->once();

        Process::fake([' --group=*' => Process::result(exitCode: 0)]);

        $handler = new RelationsHandler();
        $handler->config($mockCommand);
        $handler->runTest('');
    }

    public function testRunTestFailure()
    {
        ProjectContainer::clear();

        $mockCommand = Mockery::mock(Command::class);
        $mockCommand->shouldReceive('info')->once();

        Process::fake([' --group=*' => Process::result(exitCode: 1)]);

        $handler = new RelationsHandler();
        $handler->config($mockCommand);
        $handler->runTest('');
    }

    public function testModel()
    {
        $handler = new RelationsHandler();
        $handler->config(new Command());
        $this->assertInstanceOf(DefineRelation::class, $handler->model(''));
    }

    public function testShowModelTable()
    {
        $mockCommand = Mockery::mock(Command::class);
        $mockCommand->shouldReceive('table')->once();
        $handler = new RelationsHandler();
        $handler->config($mockCommand);
        $handler->showModelTable();
    }

    public function testWriteMarkdownSuccess()
    {
        $mockGenerateMarkdownFile = Mockery::mock(GenerateMarkdownFile::class);
        $mockGenerateMarkdownFile->expects('writeFile')->once()->andReturn(null);

        $mockCommand = Mockery::mock(Command::class);
        $mockCommand->shouldReceive('info')->once();

        $handler = new RelationsHandler(
            new Filesystem(),
            new GenerateProjectTestFile(),
            $mockGenerateMarkdownFile
        );
        $handler->config($mockCommand);
        $handler->writeMarkdown('');
    }

    public function testWriteMarkdownFailure()
    {
        $mockGenerateMarkdownFile = Mockery::mock(GenerateMarkdownFile::class);
        $mockGenerateMarkdownFile->expects('writeFile')->once()->andReturn('error');

        $mockCommand = Mockery::mock(Command::class);
        $mockCommand->shouldReceive('error')->once();

        $handler = new RelationsHandler(
            new Filesystem(),
            new GenerateProjectTestFile(),
            $mockGenerateMarkdownFile
        );
        $handler->config($mockCommand);
        $handler->writeMarkdown('');
    }
}
