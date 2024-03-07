<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Writer;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Writer\GenerateRelationTypesTrait;

class GenerateRelationshipsForPrimaryModelTraitTest extends TestCase
{
    public function testGetContent()
    {
        $this->assertIsString(GenerateRelationTypesTrait::getContent());
    }
}
