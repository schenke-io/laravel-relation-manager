<?php

use SchenkeIo\LaravelRelationManager\Data\RelationshipData;

it('can load valid_relationships.json', function () {
    $path = realpath(__DIR__.'/../../Data/valid_relationships.json');
    $relationshipData = RelationshipData::loadFromFile($path);

    expect($relationshipData)->toBeInstanceOf(RelationshipData::class);
    expect($relationshipData->models)->toHaveKey('SchenkeIo\\LaravelRelationManager\\Tests\\Models\\User');
});

it('returns null for invalid_syntax.json', function () {
    $path = realpath(__DIR__.'/../../Data/invalid_syntax.json');
    $relationshipData = RelationshipData::loadFromFile($path);

    expect($relationshipData)->toBeNull();
});

it('validates missing models from missing_models.json', function () {
    $path = realpath(__DIR__.'/../../Data/missing_models.json');
    $relationshipData = RelationshipData::loadFromFile($path);

    $errors = $relationshipData->validateImplementation([]);
    expect($errors)->toContain('Model SchenkeIo\\LaravelRelationManager\\Tests\\Models\\NonExistent missing in implementation');
});

it('detects mismatched relations from mismatched_relations.json', function () {
    $path = realpath(__DIR__.'/../../Data/mismatched_relations.json');
    $relationshipData = RelationshipData::loadFromFile($path);

    $currentModels = [
        'SchenkeIo\\LaravelRelationManager\\Tests\\Models\\User' => [
            'posts' => [
                'type' => \SchenkeIo\LaravelRelationManager\Enums\Relation::hasMany,
                'related' => 'SchenkeIo\\LaravelRelationManager\\Tests\\Models\\Post',
            ],
        ],
    ];

    $errors = $relationshipData->validateImplementation($currentModels);
    expect($errors)->toContain('Relation SchenkeIo\\LaravelRelationManager\\Tests\\Models\\User::posts type mismatch: expected belongsToMany, got hasMany');
});
