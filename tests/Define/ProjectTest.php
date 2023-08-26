<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Define;

use Illuminate\Console\Command;
use Mockery\MockInterface;
use SchenkeIo\LaravelRelationManager\Data\ProjectData;
use SchenkeIo\LaravelRelationManager\Define\Project;
use SchenkeIo\LaravelRelationManager\Exceptions\InvalidClassException;
use SchenkeIo\LaravelRelationManager\Tests\database\Models\Single;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use SchenkeIo\LaravelRelationManager\Writer\SaveFileContent;

class ProjectTest extends TestCase
{
    public function testInitialErrorsAreWrittenAsWarnings()
    {
        /** @var Command $commandMock - Phpstorm overwriting */
        $commandMock = $this->mock(Command::class, function (MockInterface $mock) {
            $mock->shouldReceive('warn')->times(2);
            $mock->shouldReceive('getName')->once();
        });
        /** @var SaveFileContent $saveFileContentMock */
        $saveFileContentMock = $this->mock(SaveFileContent::class, function (MockInterface $mock) {
            $mock->shouldReceive('saveContent')->zeroOrMoreTimes();
        });
        $project = new Project(
            new ProjectData([
                sayEach('')->belongsToMany(''),
            ], $commandMock),
            $saveFileContentMock
        );
    }

    public function testAddModelDirectory()
    {
        /** @var Command $commandMock - Phpstorm overwriting */
        $commandMock = $this->mock(Command::class, function (MockInterface $mock) {
            $mock->shouldReceive('getName')->once();
        });
        /** @var SaveFileContent $saveFileContentMock */
        $saveFileContentMock = $this->mock(SaveFileContent::class, function (MockInterface $mock) {
            $mock->shouldReceive('saveContent')->zeroOrMoreTimes();
        });
        $project = new Project(
            new ProjectData([], $commandMock),
            $saveFileContentMock
        );
        $this->assertInstanceOf(Project::class, $project->addModelDirectories([]));
    }

    public function testWriteMermaidMarkdown()
    {
        /** @var Command $commandMock - Phpstorm overwriting */
        $commandMock = $this->mock(Command::class, function (MockInterface $mock) {
            $mock->shouldReceive('getName')->zeroOrMoreTimes();
        });
        /** @var SaveFileContent $saveFileContentMock */
        $saveFileContentMock = $this->mock(SaveFileContent::class, function (MockInterface $mock) {
            $mock->shouldReceive('saveContent')->once();
        });

        $project = new Project(
            new ProjectData([], $commandMock),
            $saveFileContentMock
        );

        $project->writeMermaidMarkdown(__FILE__, false);
    }

    /**
     * @throws InvalidClassException
     */
    public function testWriteTestFileClassPhpunitInvalidClass()
    {
        /** @var Command $commandMock - Phpstorm overwriting */
        $commandMock = $this->mock(Command::class, function (MockInterface $mock) {
            $mock->shouldReceive('getName')->once();
            $mock->shouldReceive('error')->once();
        });
        /** @var SaveFileContent $saveFileContentMock */
        $saveFileContentMock = $this->mock(SaveFileContent::class, function (MockInterface $mock) {
            $mock->shouldReceive('saveContent')->zeroOrMoreTimes();
        });

        $project = new Project(
            new ProjectData([
                sayEach(Single::class)->isSingle(),
            ], $commandMock),
            $saveFileContentMock
        );
        $project->writeTestFileClassPhpunit('');
    }

    /**
     * @throws InvalidClassException
     */
    public function testWriteTestFileClassPhpunitValidClass()
    {
        /** @var Command $commandMock - Phpstorm overwriting */
        $commandMock = $this->mock(Command::class, function (MockInterface $mock) {
            $mock->shouldReceive('getName')->once();
            $mock->shouldReceive('error')->once();
        });
        /** @var SaveFileContent $saveFileContentMock */
        $saveFileContentMock = $this->mock(SaveFileContent::class, function (MockInterface $mock) {
            $mock->shouldReceive('saveContent')->zeroOrMoreTimes();
        });

        $project = new Project(
            new ProjectData([
                sayEach(Single::class)->isSingle(),
            ], $commandMock),
            $saveFileContentMock
        );
        $project->writeTestFileClassPhpunit(__FILE__, false);
    }
}
