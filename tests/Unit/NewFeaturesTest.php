<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit;

use SchenkeIo\LaravelRelationManager\Data\ConfigData;
use SchenkeIo\LaravelRelationManager\Data\ModelData;
use SchenkeIo\LaravelRelationManager\Data\RelationData;
use SchenkeIo\LaravelRelationManager\Data\RelationshipData;
use SchenkeIo\LaravelRelationManager\Enums\DiagramDirection;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use SchenkeIo\LaravelRelationManager\Writer\GetDiagram;

class NewFeaturesTest extends TestCase
{
    public function test_show_intermediate_tables_config_works()
    {
        $models = [
            'App\Models\User' => new ModelData(methods: [
                'roles' => new RelationData(type: EloquentRelation::belongsToMany, related: 'App\Models\Role'),
            ]),
        ];

        // Default: false
        $relationshipData = new RelationshipData(new ConfigData, $models);
        $diagramData = $relationshipData->getDiagramData();
        $this->assertArrayNotHasKey('role_user', $diagramData);

        // Explicitly true in call
        $diagramData = $relationshipData->getDiagramData(true);
        $this->assertArrayHasKey('role_user', $diagramData);

        // true in config
        $config = new ConfigData(showIntermediateTables: true);
        $relationshipData = new RelationshipData($config, $models);
        $diagramData = $relationshipData->getDiagramData();
        $this->assertArrayHasKey('role_user', $diagramData);
    }

    public function test_mermaid_uses_dashed_arrows_for_one_way_relations()
    {
        $data = [
            'posts' => [
                'users' => EloquentRelation::belongsTo,
            ],
        ];
        $mermaid = GetDiagram::getMermaidCode($data, DiagramDirection::LR);
        $this->assertStringContainsString('posts -.-> users', $mermaid);
    }

    public function test_polymorphic_nodes_are_styled_with_rounded_corners_and_css_class()
    {
        $data = [
            'images' => [
                null => EloquentRelation::morphTo,
            ],
        ];
        $mermaid = GetDiagram::getMermaidCode($data, DiagramDirection::LR);
        $this->assertStringContainsString('images(images)', $mermaid);
        $this->assertStringContainsString('classDef poly', $mermaid);
        $this->assertStringContainsString('class images poly', $mermaid);
    }

    public function test_arrows_in_polymorphic_relationships_point_toward_polymorphic_model()
    {
        $models = [
            'App\Models\Post' => new ModelData(methods: [
                'images' => new RelationData(type: EloquentRelation::morphMany, related: 'App\Models\Image'),
            ]),
        ];
        $relationshipData = new RelationshipData(new ConfigData, $models);
        $diagramData = $relationshipData->getDiagramData();

        // Post morphMany Image => posts -> images
        $this->assertArrayHasKey('posts', $diagramData);
        $this->assertArrayHasKey('images', $diagramData['posts']);
        $this->assertEquals(EloquentRelation::morphMany, $diagramData['posts']['images']);

        $mermaid = GetDiagram::getMermaidCode($diagramData, DiagramDirection::LR);
        $this->assertStringContainsString('posts -.-> images', $mermaid);
        $this->assertStringContainsString('images(images)', $mermaid);
    }

    public function test_bidirectional_relationships_are_detected_and_styled()
    {
        $models = [
            'App\Models\Post' => new ModelData(methods: [
                'comments' => new RelationData(type: EloquentRelation::hasMany, related: 'App\Models\Comment'),
            ]),
            'App\Models\Comment' => new ModelData(methods: [
                'post' => new RelationData(type: EloquentRelation::belongsTo, related: 'App\Models\Post'),
            ]),
        ];
        $relationshipData = new RelationshipData(new ConfigData, $models);
        $diagramData = $relationshipData->getDiagramData();

        // Should be merged into one bidirectional link
        $this->assertCount(1, $diagramData);
        $this->assertEquals(EloquentRelation::bidirectional, current($diagramData)[key(current($diagramData))]);

        $mermaid = GetDiagram::getMermaidCode($diagramData, DiagramDirection::LR);
        $this->assertStringContainsString('<==>', $mermaid);
    }
}
