<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Scanner;

use SchenkeIo\LaravelRelationManager\Enums\Relation;
use SchenkeIo\LaravelRelationManager\Scanner\ModelScanner;
use SchenkeIo\LaravelRelationManager\Tests\Models\Comment;
use SchenkeIo\LaravelRelationManager\Tests\Models\Country;
use SchenkeIo\LaravelRelationManager\Tests\Models\MorphModel;
use SchenkeIo\LaravelRelationManager\Tests\Models\Post;
use SchenkeIo\LaravelRelationManager\Tests\Models\Profile;
use SchenkeIo\LaravelRelationManager\Tests\Models\Tag;
use SchenkeIo\LaravelRelationManager\Tests\Models\ThroughModel;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class ModelScannerAdvancedTest extends TestCase
{
    public function test_it_can_scan_morph_relations()
    {
        $scanner = new ModelScanner;
        $results = $scanner->scan(__DIR__.'/../../Models');

        $this->assertArrayHasKey(MorphModel::class, $results);
        $morph = $results[MorphModel::class];

        $this->assertEquals(Relation::morphTo, $morph['imageable']['type']);
        $this->assertEquals(Relation::morphOne, $morph['profile']['type']);
        $this->assertEquals(Profile::class, $morph['profile']['related']);

        $this->assertEquals(Relation::morphMany, $morph['comments']['type']);
        $this->assertEquals(Comment::class, $morph['comments']['related']);

        $this->assertEquals(Relation::morphToMany, $morph['tags']['type']);
        $this->assertEquals(Tag::class, $morph['tags']['related']);
    }

    public function test_it_can_scan_through_relations()
    {
        $scanner = new ModelScanner;
        $results = $scanner->scan(__DIR__.'/../../Models');

        $this->assertArrayHasKey(ThroughModel::class, $results);
        $through = $results[ThroughModel::class];

        $this->assertEquals(Relation::hasOneThrough, $through['userCountry']['type']);
        $this->assertEquals(Country::class, $through['userCountry']['related']);

        $this->assertEquals(Relation::hasManyThrough, $through['userPosts']['type']);
        $this->assertEquals(Post::class, $through['userPosts']['related']);
    }
}
