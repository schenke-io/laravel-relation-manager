<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature;

use SchenkeIo\LaravelRelationManager\Enums\Relation;
use SchenkeIo\LaravelRelationManager\Facades\ModelScanner;
use SchenkeIo\LaravelRelationManager\Tests\Models\Post;
use SchenkeIo\LaravelRelationManager\Tests\Models\User;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class PestExpectationsTest extends TestCase
{
    public function test_to_have_relation()
    {
        ModelScanner::shouldReceive('scan')->andReturn([
            User::class => [
                'posts' => [
                    'type' => Relation::hasMany,
                    'related' => Post::class,
                ],
            ],
        ]);

        expect(User::class)->toHaveRelation(Post::class, Relation::hasMany);
    }

    public function test_to_has_many()
    {
        ModelScanner::shouldReceive('scan')->andReturn([
            User::class => [
                'posts' => [
                    'type' => Relation::hasMany,
                    'related' => Post::class,
                ],
            ],
        ]);

        expect(User::class)->toHasMany(Post::class);
    }

    public function test_to_has_one()
    {
        ModelScanner::shouldReceive('scan')->andReturn([
            User::class => [
                'profile' => [
                    'type' => Relation::hasOne,
                    'related' => 'Profile',
                ],
            ],
        ]);

        expect(User::class)->toHasOne('Profile');
    }

    public function test_to_belongs_to()
    {
        ModelScanner::shouldReceive('scan')->andReturn([
            Post::class => [
                'user' => [
                    'type' => Relation::belongsTo,
                    'related' => User::class,
                ],
            ],
        ]);

        expect(Post::class)->toBelongsTo(User::class);
    }

    public function test_to_belongs_to_many()
    {
        ModelScanner::shouldReceive('scan')->andReturn([
            User::class => [
                'roles' => [
                    'type' => Relation::belongsToMany,
                    'related' => 'Role',
                ],
            ],
        ]);

        expect(User::class)->toBelongsToMany('Role');
    }

    public function test_fails_when_relation_missing()
    {
        ModelScanner::shouldReceive('scan')->andReturn([
            User::class => [],
        ]);

        $this->expectException(\PHPUnit\Framework\ExpectationFailedException::class);
        expect(User::class)->toHasMany(Post::class);
    }

    public function test_fails_when_model_missing()
    {
        ModelScanner::shouldReceive('scan')->andReturn([]);

        $this->expectException(\PHPUnit\Framework\ExpectationFailedException::class);
        expect(User::class)->toHasMany(Post::class);
    }
}
