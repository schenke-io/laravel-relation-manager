<?php

use SchenkeIo\LaravelRelationManager\Enums\Relation;
use SchenkeIo\LaravelRelationManager\Scanner\ModelScanner;
use SchenkeIo\LaravelRelationManager\Tests\Models\AttributeModel;
use SchenkeIo\LaravelRelationManager\Tests\Models\Post;
use SchenkeIo\LaravelRelationManager\Tests\Models\Role;
use SchenkeIo\LaravelRelationManager\Tests\Models\User;

test('it can scan models and find relations', function () {
    $scanner = new ModelScanner;
    $results = $scanner->scan(__DIR__.'/../../Models');

    expect($results)->toBeArray()
        ->and($results)->toHaveKey(User::class)
        ->and($results)->toHaveKey(Post::class)
        ->and($results)->toHaveKey(Role::class)
        ->and($results)->toHaveKey(AttributeModel::class);

    expect($results[User::class]['posts'])->toMatchArray([
        'type' => Relation::hasMany,
        'related' => Post::class,
    ]);

    expect($results[User::class]['roles'])->toMatchArray([
        'type' => Relation::belongsToMany,
        'related' => Role::class,
        'pivotTable' => 'role_user',
    ]);

    expect($results[Role::class]['users'])->toMatchArray([
        'type' => Relation::belongsToMany,
        'related' => User::class,
        'pivotTable' => 'role_user',
    ]);

    expect($results[Post::class]['author'])->toMatchArray([
        'type' => Relation::belongsTo,
        'related' => User::class,
    ]);

    expect($results[AttributeModel::class]['customRelation'])->toMatchArray([
        'type' => Relation::hasOne,
        'related' => User::class,
    ]);
});
