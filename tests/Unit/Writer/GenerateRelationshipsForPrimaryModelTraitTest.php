<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Writer;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Writer\GenerateRelationTypesTrait;

class GenerateRelationshipsForPrimaryModelTraitTest extends TestCase
{
    public function test_get_content()
    {
        $this->assertIsString(GenerateRelationTypesTrait::getContent());
    }
}
