<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Writer;

class GenerateMarkdownFileTest
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
