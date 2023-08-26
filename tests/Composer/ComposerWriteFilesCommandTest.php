<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Composer;

use Mockery\MockInterface;
use SchenkeIo\LaravelRelationManager\Composer\ComposerWriteFilesCommand;
use SchenkeIo\LaravelRelationManager\Exceptions\DirectoryNotWritableException;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use SchenkeIo\LaravelRelationManager\Writer\SaveFileContent;

class ComposerWriteFilesCommandTest extends TestCase
{
    /**
     * @throws DirectoryNotWritableException
     */
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
