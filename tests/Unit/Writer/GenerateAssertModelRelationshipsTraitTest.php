<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Writer;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Writer\GenerateRelationTypesTrait;

class GenerateAssertModelRelationshipsTraitTest extends TestCase
{
    public function test_get_content(): void
    {
        $this->assertIsString(GenerateRelationTypesTrait::getContent());
    }
}
