<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Writer;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Data\ProjectData;
use SchenkeIo\LaravelRelationManager\Tests\database\Models\Capital;
use SchenkeIo\LaravelRelationManager\Tests\database\Models\Country;
use SchenkeIo\LaravelRelationManager\Writer\GenerateMermaidMarkdown;

class GenerateMermaidMarkdownTest extends TestCase
{
    public function testGetContent()
    {
        $this->assertIsString(GenerateMermaidMarkdown::getContent(
            new ProjectData([
                sayEach(Country::class)->hasOne(Capital::class),
            ])
        ));
    }
}
