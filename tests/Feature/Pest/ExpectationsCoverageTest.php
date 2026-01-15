<?php

use SchenkeIo\LaravelRelationManager\Enums\Relation;
use SchenkeIo\LaravelRelationManager\Facades\ModelScanner;
use SchenkeIo\LaravelRelationManager\Tests\Models\Post;
use SchenkeIo\LaravelRelationManager\Tests\Models\User;

test('toHaveRelation coverage', function () {
    ModelScanner::shouldReceive('scan')->andReturn([
        User::class => [
            'posts' => [
                'type' => Relation::hasMany,
                'related' => Post::class,
            ],
        ],
    ]);

    expect(User::class)->toHaveRelation(Post::class, Relation::hasMany);
});

test('toHasMany coverage', function () {
    ModelScanner::shouldReceive('scan')->andReturn([
        User::class => [
            'posts' => [
                'type' => Relation::hasMany,
                'related' => Post::class,
            ],
        ],
    ]);

    expect(User::class)->toHasMany(Post::class);
});

test('toHasOne coverage', function () {
    ModelScanner::shouldReceive('scan')->andReturn([
        User::class => [
            'profile' => [
                'type' => Relation::hasOne,
                'related' => 'Profile',
            ],
        ],
    ]);

    expect(User::class)->toHasOne('Profile');
});

test('toBelongsTo coverage', function () {
    ModelScanner::shouldReceive('scan')->andReturn([
        Post::class => [
            'user' => [
                'type' => Relation::belongsTo,
                'related' => User::class,
            ],
        ],
    ]);

    expect(Post::class)->toBelongsTo(User::class);
});

test('toBelongsToMany coverage', function () {
    ModelScanner::shouldReceive('scan')->andReturn([
        User::class => [
            'roles' => [
                'type' => Relation::belongsToMany,
                'related' => 'Role',
            ],
        ],
    ]);

    expect(User::class)->toBelongsToMany('Role');
});
