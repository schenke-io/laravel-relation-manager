<?php

namespace SchenkeIo\LaravelRelationshipManager\Tests\Composer;

use Mockery\MockInterface;
use SchenkeIo\LaravelRelationshipManager\Composer\ComposerWriteFilesCommand;
use SchenkeIo\LaravelRelationshipManager\Tests\TestCase;
use SchenkeIo\LaravelRelationshipManager\Writer\SaveFileContent;

class ComposerWriteFilesCommandTest extends TestCase
{
    public function testRun()
    {
        /** @var SaveFileContent $saveFileContentMock */
        $saveFileContentMock = $this->mock(SaveFileContent::class, function (MockInterface $mock) {
            $mock->expects('saveContent')->zeroOrMoreTimes();
        });
        ComposerWriteFilesCommand::run($saveFileContentMock);
        $composerJson = json_decode(file_get_contents(__DIR__.'/../../composer.json'), true);
        $this->assertStringContainsString('ComposerWriteFilesCommand::run', $composerJson['scripts']['write-class']);
    }

    public function testRelationshipFileIsOk()
    {
        $this->assertIsBool(ComposerWriteFilesCommand::relationshipFileIsOk());
    }

    public function testAssertFileIsOk()
    {
        $this->assertIsBool(ComposerWriteFilesCommand::assertFileIsOk());
    }
}
