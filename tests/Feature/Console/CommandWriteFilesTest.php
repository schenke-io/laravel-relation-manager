<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Console;

use Illuminate\Filesystem\Filesystem;
use Mockery\MockInterface;
use SchenkeIo\LaravelRelationManager\Console\WriteTraitFiles;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class CommandWriteFilesTest extends TestCase
{
    public function testRun()
    {
        /** @var Filesystem $mockFilesystem */
        $mockFilesystem = $this->mock(Filesystem::class, function (MockInterface $mock) {
            $mock->expects('put')->zeroOrMoreTimes();
        });
        $writeCodeFiles = new WriteTraitFiles($mockFilesystem);
        $writeCodeFiles->generate();
    }
}
