<?php

namespace SchenkeIo\LaravelRelationshipManager\Tests\Writer;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationshipManager\Data\ProjectData;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Capital;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Country;
use SchenkeIo\LaravelRelationshipManager\Writer\GenerateMermaidMarkdown;

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
