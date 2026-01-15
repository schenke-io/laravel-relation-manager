<?php

use SchenkeIo\LaravelRelationManager\Data\RelationData;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;

test('RelationData can be instantiated from array with string type', function () {
    $data = [
        'type' => 'hasMany',
        'related' => 'App\Models\Post',
    ];

    $relationData = RelationData::from($data);

    expect($relationData->type)->toBe(EloquentRelation::hasMany);
});
