<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Data;

use PHPUnit\Framework\TestCase;

class ModelCountDataTest extends TestCase
{
    public function testConstructor()
    {
        $data = new \SchenkeIo\LaravelRelationManager\Data\ModelCountData('model-name', 4);
        $this->assertEquals(4, $data->count);
        $this->assertEquals('model-name', $data->model);
        $this->assertInstanceOf(\SchenkeIo\LaravelRelationManager\Data\ModelCountData::class, $data);
    }
}
