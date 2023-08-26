<?php

namespace SchenkeIo\LaravelRelationshipManager\Tests\Writer;

use PhpUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationshipManager\Exceptions\DirectoryNotWritableException;
use SchenkeIo\LaravelRelationshipManager\Writer\SaveFileContent;

class SaveFileContentTest extends TestCase
{
    public function testSaveContent()
    {
        $filename = __DIR__.'/dummy.txt';
        $content = '123';
        $this->assertFileDoesNotExist($filename);
        (new SaveFileContent)->saveContent($filename, $content);
        $this->assertFileExists($filename);
        unlink($filename);
        $this->assertFileDoesNotExist($filename);
    }

    public function testSaveContentError()
    {
        $this->expectException(DirectoryNotWritableException::class);
        (new SaveFileContent)->saveContent('', '');
    }
}
