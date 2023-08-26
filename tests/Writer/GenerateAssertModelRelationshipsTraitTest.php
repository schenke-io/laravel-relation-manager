<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Writer;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Writer\GenerateRelationshipsForPrimaryModelTrait;

class GenerateAssertModelRelationshipsTraitTest extends TestCase
{
    public function testGetContent(): void
    {
        $this->assertIsString(GenerateRelationshipsForPrimaryModelTrait::getContent());
    }
}
