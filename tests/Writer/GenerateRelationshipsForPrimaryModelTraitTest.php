<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Writer;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Writer\GenerateRelationshipsForPrimaryModelTrait;

class GenerateRelationshipsForPrimaryModelTraitTest extends TestCase
{
    public function testGetContent()
    {
        $this->assertIsString(GenerateRelationshipsForPrimaryModelTrait::getContent());
    }
}
