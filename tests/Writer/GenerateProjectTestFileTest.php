<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Writer;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Data\ProjectData;
use SchenkeIo\LaravelRelationManager\Exceptions\InvalidClassException;
use SchenkeIo\LaravelRelationManager\Writer\GenerateProjectTestFile;

class GenerateProjectTestFileTest extends TestCase
{
    /**
     * @throws InvalidClassException
     */
    public function testGetContentInvalid()
    {
        $this->expectException(InvalidClassException::class);
        $this->assertIsString(GenerateProjectTestFile::getContent(new ProjectData([]), ClassData::take('')));
    }

    public function testGetContentValid()
    {
        $this->assertIsString(GenerateProjectTestFile::getContent(new ProjectData([]), ClassData::take(__CLASS__)));
    }
}
