<?php

namespace SchenkeIo\LaravelRelationshipManager\Tests\Writer;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationshipManager\Writer\GenerateRelationshipsForPrimaryModelTrait;

class GenerateRelationshipsForPrimaryModelTraitTest extends TestCase
{
    public function testGetContent()
    {
        $this->assertIsString(GenerateRelationshipsForPrimaryModelTrait::getContent());
    }
}
