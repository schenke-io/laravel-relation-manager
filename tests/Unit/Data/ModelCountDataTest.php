<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Data;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Data\ModelCountData;

class ModelCountDataTest extends TestCase
{
    public function testConstructor()
    {
        $data = new ModelCountData('model-name', 4);
        $this->assertEquals(4, $data->count);
        $this->assertEquals('model-name', $data->model);
        $this->assertInstanceOf(ModelCountData::class, $data);
    }
}
