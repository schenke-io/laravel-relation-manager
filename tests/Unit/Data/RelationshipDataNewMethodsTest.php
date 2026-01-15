<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Data;

use SchenkeIo\LaravelRelationManager\Data\ConfigData;
use SchenkeIo\LaravelRelationManager\Data\ModelData;
use SchenkeIo\LaravelRelationManager\Data\RelationData;
use SchenkeIo\LaravelRelationManager\Data\RelationshipData;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class RelationshipDataNewMethodsTest extends TestCase
{
    public function test_get_model_relations_data()
    {
        $models = [
            'App\Models\User' => new ModelData(methods: [
                'posts' => new RelationData(type: EloquentRelation::hasMany, related: 'App\Models\Post'),
                'profile' => new RelationData(type: EloquentRelation::hasOne, related: 'App\Models\Profile'),
                'recentPosts' => new RelationData(type: EloquentRelation::hasManyThrough, related: 'App\Models\Post'),
            ]),
            'App\Models\Post' => new ModelData(methods: [
                'author' => new RelationData(type: EloquentRelation::belongsTo, related: 'App\Models\User'),
            ]),
            'App\Models\Guest' => new ModelData(methods: [
                'none' => new RelationData(type: EloquentRelation::noRelation),
            ]),
        ];

        $relationshipData = new RelationshipData(config: new ConfigData, models: $models);
        $data = $relationshipData->getModelRelationsData();

        $this->assertArrayHasKey('User', $data);
        $this->assertCount(2, $data['User']['direct']);
        $this->assertCount(1, $data['User']['indirect']);
        $this->assertContains('posts (Post)', $data['User']['direct']);
        $this->assertContains('profile (Profile)', $data['User']['direct']);
        $this->assertContains('recentPosts (Post)', $data['User']['indirect']);

        $this->assertArrayHasKey('Post', $data);
        $this->assertCount(1, $data['Post']['direct']);
        $this->assertContains('author (User)', $data['Post']['direct']);

        $this->assertArrayHasKey('Guest', $data);
        $this->assertEmpty($data['Guest']['direct']);
        $this->assertEmpty($data['Guest']['indirect']);
    }

    public function test_get_database_table_data()
    {
        $models = [
            'App\Models\User' => new ModelData(methods: [
                'roles' => new RelationData(type: EloquentRelation::belongsToMany, related: 'App\Models\Role'),
                'image' => new RelationData(type: EloquentRelation::morphOne, related: 'App\Models\Image'),
            ]),
            'App\Models\Post' => new ModelData(methods: [
                'author' => new RelationData(type: EloquentRelation::belongsTo, related: 'App\Models\User', foreignKey: 'user_id'),
                'tags' => new RelationData(type: EloquentRelation::morphToMany, related: 'App\Models\Tag', pivotTable: 'taggables'),
            ]),
            'App\Models\Comment' => new ModelData(methods: [
                'commentable' => new RelationData(type: EloquentRelation::morphTo),
            ]),
        ];

        $relationshipData = new RelationshipData(config: new ConfigData, models: $models);
        $data = $relationshipData->getDatabaseTableData();

        // User table has no foreign keys in this setup (belongsToMany and morphOne don't put FK in User table)
        // Wait, morphOne puts FK in related table (images).
        $this->assertArrayHasKey('users', $data);

        // Post table has author (user_id)
        $this->assertArrayHasKey('posts', $data);
        $this->assertContains('user_id', $data['posts']);

        // Comment table has commentable_id, commentable_type
        $this->assertArrayHasKey('comments', $data);
        $this->assertContains('commentable_id', $data['comments']);
        $this->assertContains('commentable_type', $data['comments']);

        // roles-user pivot table
        $this->assertArrayHasKey('role_user', $data);
        $this->assertContains('role_id', $data['role_user']);
        $this->assertContains('user_id', $data['role_user']);

        // taggables pivot table
        $this->assertArrayHasKey('taggables', $data);
        $this->assertContains('tag_id', $data['taggables']);
        // for morphToMany, it should have taggable_id and taggable_type
        // Wait, I need to know what the name is. For morphToMany, it's often 'taggable'.
        // I'll need to see how I can determine this name.
    }

    public function test_get_reverse_relations()
    {
        $models = [
            'App\Models\User' => new ModelData(methods: [
                'posts' => new RelationData(type: EloquentRelation::hasMany, related: 'App\Models\Post'),
            ]),
            'App\Models\Post' => new ModelData(methods: [
                'author' => new RelationData(type: EloquentRelation::belongsTo, related: 'App\Models\User'),
            ]),
        ];

        $relationshipData = new RelationshipData(config: new ConfigData, models: $models);

        $reverse = $relationshipData->getReverseRelations('App\Models\User', 'posts');
        $this->assertCount(1, $reverse);
        $this->assertEquals(['App\Models\Post', 'author'], $reverse[0]);

        $reverse = $relationshipData->getReverseRelations('App\Models\Post', 'author');
        $this->assertCount(1, $reverse);
        $this->assertEquals(['App\Models\User', 'posts'], $reverse[0]);

        // cover line 439
        $this->assertEquals([], $relationshipData->getReverseRelations('Invalid', 'method'));
    }

    public function test_get_morph_to_targets()
    {
        $models = [
            'App\Models\Post' => new ModelData(methods: [
                'comments' => new RelationData(type: EloquentRelation::morphMany, related: 'App\Models\Comment'),
            ]),
            'App\Models\Video' => new ModelData(methods: [
                'comments' => new RelationData(type: EloquentRelation::morphMany, related: 'App\Models\Comment'),
            ]),
            'App\Models\Comment' => new ModelData(methods: [
                'commentable' => new RelationData(type: EloquentRelation::morphTo),
                'notMorph' => new RelationData(type: EloquentRelation::belongsTo, related: 'App\Models\Post'),
            ]),
        ];

        $relationshipData = new RelationshipData(config: new ConfigData, models: $models);

        $targets = $relationshipData->getMorphToTargets('App\Models\Comment', 'commentable');
        $this->assertCount(2, $targets);
        $this->assertContains('App\Models\Post', $targets);
        $this->assertContains('App\Models\Video', $targets);

        // cover line 466
        $this->assertEquals([], $relationshipData->getMorphToTargets('App\Models\Comment', 'notMorph'));
    }
}
