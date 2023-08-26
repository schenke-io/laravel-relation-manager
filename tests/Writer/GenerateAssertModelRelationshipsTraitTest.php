<?php

namespace SchenkeIo\LaravelRelationshipManager\Tests\Writer;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationshipManager\Writer\GenerateRelationshipsForPrimaryModelTrait;

class GenerateAssertModelRelationshipsTraitTest extends TestCase
{
    public function testGetContent(): void
    {
        $this->assertIsString(GenerateRelationshipsForPrimaryModelTrait::getContent());
    }
}
