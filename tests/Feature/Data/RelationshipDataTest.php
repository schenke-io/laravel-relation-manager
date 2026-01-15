<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Data;

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Data\RelationshipData;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class RelationshipDataTest extends TestCase
{
    public function test_it_loads_from_file()
    {
        $path = base_path('.relationships.json');
        File::shouldReceive('exists')->with($path)->andReturn(true);
        File::shouldReceive('get')->with($path)->andReturn(json_encode([
            'config' => [
                'markdown_path' => 'docs/relations.md',
            ],
            'models' => [
                'App\Models\User' => [
                    'posts' => [
                        'type' => 'hasMany',
                        'related' => 'App\Models\Post',
                    ],
                ],
            ],
        ]));

        $relationshipData = RelationshipData::loadFromFile($path);

        $this->assertInstanceOf(RelationshipData::class, $relationshipData);
        $this->assertEquals('docs/relations.md', $relationshipData->config->markdownPath);
        $this->assertArrayHasKey('App\Models\User', $relationshipData->models);
        $this->assertArrayHasKey('posts', $relationshipData->models['App\Models\User']->methods);
    }
}
