<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Enums;

use SchenkeIo\LaravelRelationManager\Enums\ConfigKey;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class ConfigKeyTest extends TestCase
{
    public function test_has_all_defined()
    {
        foreach (ConfigKey::cases() as $case) {
            $this->assertIsString($case->definition());
        }
    }
}
