<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Writer;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Mockery;
use SchenkeIo\LaravelRelationManager\Data\ConfigData;
use SchenkeIo\LaravelRelationManager\Data\ModelData;
use SchenkeIo\LaravelRelationManager\Data\RelationData;
use SchenkeIo\LaravelRelationManager\Data\RelationshipData;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;
use SchenkeIo\LaravelRelationManager\Support\PathResolver;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use SchenkeIo\LaravelRelationManager\Writer\GenerateMarkdownFile;
use SchenkeIo\LaravelRelationManager\Writer\GetDiagram;

class GenerateMarkdownFileTest extends TestCase
{
    public function test_it_generates_markdown_content()
    {
        $models = [
            'App\Models\Post' => new ModelData(methods: [
                'user' => new RelationData(type: EloquentRelation::belongsTo, related: 'App\Models\User'),
            ]),
        ];
        $relationshipData = new RelationshipData(config: new ConfigData, models: $models);
        $generator = new GenerateMarkdownFile($relationshipData);

        $markdown = $generator->getModelRelationsTable();
        $this->assertStringContainsString('Post', $markdown);
        $this->assertStringContainsString('user (User)', $markdown);

        $markdown = $generator->getDatabaseTable();
        $this->assertStringContainsString('posts', $markdown);
        $this->assertStringContainsString('user_id', $markdown);

        $diagram = $generator->getMermaidDiagram();
        $this->assertStringContainsString('mermaid', $diagram);
        $this->assertStringContainsString('posts -.-> users', $diagram);
    }

    public function test_generate_uses_mermaid_by_default()
    {
        File::shouldReceive('put')->with(PathResolver::getRealBasePath('test.md'), Mockery::on(function ($content) {
            return str_contains($content, 'mermaid');
        }))->andReturn(true);

        $relationshipData = new RelationshipData(new ConfigData, []);
        $generator = new GenerateMarkdownFile($relationshipData);

        $this->assertTrue($generator->generate('test.md'));
    }

    public function test_generate_uses_graphviz_when_configured()
    {
        Process::fake();
        File::shouldReceive('put')->with(PathResolver::getRealBasePath('test.md'), Mockery::on(function ($content) {
            return str_contains($content, "<img src='".GetDiagram::GRAPHVIZ_BASENAME.".png'");
        }))->andReturn(true);
        File::shouldReceive('put')->with(Mockery::on(fn ($path) => str_ends_with($path, '.dot')), Mockery::any())->andReturn(true);

        $config = new ConfigData(useMermaid: false);
        $relationshipData = new RelationshipData($config, []);
        $generator = new GenerateMarkdownFile($relationshipData);

        $this->assertTrue($generator->generate('test.md'));
    }

    public function test_generate_shows_warning_on_graphviz_failure()
    {
        Process::fake([
            'dot *' => Process::result('error', exitCode: 1),
        ]);
        File::shouldReceive('put')->with(PathResolver::getRealBasePath('test.md'), Mockery::on(function ($content) {
            return str_contains($content, 'Graphviz generation failed');
        }))->andReturn(true);
        File::shouldReceive('put')->with(Mockery::on(fn ($path) => str_ends_with($path, '.dot')), Mockery::any())->andReturn(true);

        $config = new ConfigData(useMermaid: false);
        $relationshipData = new RelationshipData($config, []);
        $generator = new GenerateMarkdownFile($relationshipData);

        $this->assertTrue($generator->generate('test.md'));
        $this->assertCount(1, $generator->getErrors());
    }

    public function test_generate_returns_true_on_success()
    {
        File::shouldReceive('put')->andReturn(true);
        $relationshipData = new RelationshipData(new ConfigData, []);
        $generator = new GenerateMarkdownFile($relationshipData);

        $this->assertTrue($generator->generate('test.md'));
    }

    public function test_it_can_generate_relationship_table()
    {
        $models = [
            'App\Models\Post' => new ModelData(methods: [
                'user' => new RelationData(type: EloquentRelation::belongsTo, related: 'App\Models\User'),
                'comments' => new RelationData(type: EloquentRelation::morphMany, related: 'App\Models\Comment'),
            ]),
            'App\Models\User' => new ModelData(methods: [
                'posts' => new RelationData(type: EloquentRelation::hasMany, related: 'App\Models\Post'),
            ]),
            'App\Models\Comment' => new ModelData(methods: [
                'commentable' => new RelationData(type: EloquentRelation::morphTo),
            ]),
            'App\Models\Lone' => new ModelData(methods: [
                'lonelyMorph' => new RelationData(type: EloquentRelation::morphTo),
            ]),
        ];
        $relationshipData = new RelationshipData(config: new ConfigData, models: $models);
        $generator = new GenerateMarkdownFile($relationshipData);

        $markdown = $generator->getRelationshipTable();
        $this->assertStringContainsString('Model', $markdown);
        $this->assertStringContainsString('Method', $markdown);
        $this->assertStringContainsString('Relation', $markdown);
        $this->assertStringContainsString('Related Model', $markdown);
        $this->assertStringContainsString('Post', $markdown);
        $this->assertStringContainsString('user', $markdown);
        $this->assertStringContainsString('belongsTo', $markdown);
        $this->assertStringContainsString('User', $markdown);
        $this->assertStringContainsString('commentable', $markdown);
        $this->assertStringContainsString('morphTo', $markdown);
        // commentable morphs to Post
        $this->assertStringContainsString('Post', $markdown);
        // lonelyMorph has no targets
        $this->assertStringContainsString('lonelyMorph', $markdown);
        $this->assertStringContainsString('n/a', $markdown);
    }
}
