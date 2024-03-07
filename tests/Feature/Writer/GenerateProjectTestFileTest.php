<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Writer;

class GenerateProjectTestFileTest
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
