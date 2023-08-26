<?php

namespace SchenkeIo\LaravelRelationshipManager\Tests\Writer;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationshipManager\Data\ClassData;
use SchenkeIo\LaravelRelationshipManager\Data\ProjectData;
use SchenkeIo\LaravelRelationshipManager\Exceptions\InvalidClassException;
use SchenkeIo\LaravelRelationshipManager\Writer\GenerateProjectTestFile;

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
