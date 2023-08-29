<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Writer;

use Nette\InvalidArgumentException;
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
        $this->expectException(InvalidArgumentException::class);
        $this->assertIsString(GenerateProjectTestFile::getContent(
            new ProjectData([]),
            '',
            '',
            '')
        );
    }

    public function testGetContentValid()
    {
        $classData = ClassData::take(__CLASS__);
        $this->assertIsString(
            GenerateProjectTestFile::getContent(
                new ProjectData([]),
                $classData->reflection->getShortName(),
                $classData->nameSpace,
                'PHPUnit\Framework\TestCase'
            )
        );
    }
}
