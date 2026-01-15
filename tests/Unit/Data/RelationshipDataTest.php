<?php

use SchenkeIo\LaravelRelationManager\Data\ConfigData;
use SchenkeIo\LaravelRelationManager\Data\ModelData;
use SchenkeIo\LaravelRelationManager\Data\RelationData;
use SchenkeIo\LaravelRelationManager\Data\RelationshipData;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;

test('it validates matching models and relations', function () {
    $currentModels = [
        'App\Models\User' => [
            'posts' => [
                'type' => EloquentRelation::hasMany,
                'related' => 'App\Models\Post',
            ],
        ],
    ];

    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData(methods: [
                'posts' => new RelationData(type: EloquentRelation::hasMany, related: 'App\Models\Post'),
            ]),
        ]
    );
    $errors = $relationshipData->validateImplementation($currentModels);

    expect($errors)->toBeEmpty();
});

test('it detects missing models', function () {
    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData,
        ]
    );
    $errors = $relationshipData->validateImplementation([]);

    expect($errors)->toContain('Model App\Models\User missing in implementation');
});

test('it detects type mismatch', function () {
    $currentModels = [
        'App\Models\User' => [
            'posts' => [
                'type' => EloquentRelation::hasOne,
            ],
        ],
    ];

    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData(methods: [
                'posts' => new RelationData(type: EloquentRelation::hasMany),
            ]),
        ]
    );
    $errors = $relationshipData->validateImplementation($currentModels);

    expect($errors)->toContain('EloquentRelation App\Models\User::posts type mismatch: expected hasMany, got hasOne');
});

test('it validates strict mode', function () {
    $currentModels = [
        'App\Models\User' => [
            'extra' => [],
        ],
        'App\Models\Extra' => [],
    ];

    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData,
        ]
    );
    $errors = $relationshipData->validateImplementation($currentModels, true);

    expect($errors)->toContain('Model App\Models\Extra found in implementation but missing in JSON')
        ->toContain('EloquentRelation App\Models\User::extra found in implementation but missing in JSON');
});

test('it detects asymmetry', function () {
    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData(methods: [
                'groups' => new RelationData(type: EloquentRelation::belongsToMany, related: 'App\Models\Group'),
            ]),
            'App\Models\Group' => new ModelData,
        ]
    );
    $warnings = $relationshipData->getWarnings();

    expect($warnings)->toContain('Asymmetry: App\Models\User::groups() is belongsToMany but App\Models\Group has no inverse belongsToMany.');
});

test('it detects broken relations', function () {
    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData(methods: [
                'posts' => new RelationData(type: EloquentRelation::hasMany, related: 'NonExistentClass'),
            ]),
        ]
    );
    $warnings = $relationshipData->getWarnings();

    expect($warnings)->toContain('Broken: App\Models\User::posts() points to non-existent class NonExistentClass.');
});

test('it detects missing relation methods', function () {
    $currentModels = [
        'App\Models\User' => [],
    ];
    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData(methods: [
                'posts' => new RelationData(type: EloquentRelation::hasMany, related: 'App\Models\Post'),
            ]),
        ]
    );
    $errors = $relationshipData->validateImplementation($currentModels);
    expect($errors)->toContain('EloquentRelation App\Models\User::posts missing in implementation');
});

test('it detects mismatched related model', function () {
    $currentModels = [
        'App\Models\User' => [
            'posts' => ['type' => EloquentRelation::hasMany, 'related' => 'App\Models\Other'],
        ],
    ];
    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData(methods: [
                'posts' => new RelationData(type: EloquentRelation::hasMany, related: 'App\Models\Post'),
            ]),
        ]
    );
    $errors = $relationshipData->validateImplementation($currentModels);
    expect($errors)->toContain('EloquentRelation App\Models\User::posts related model mismatch: expected App\Models\Post, got App\Models\Other');
});

test('it detects mismatched foreign key', function () {
    $currentModels = [
        'App\Models\User' => [
            'posts' => ['type' => EloquentRelation::hasMany, 'related' => 'App\Models\Post', 'foreignKey' => 'other_id'],
        ],
    ];
    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData(methods: [
                'posts' => new RelationData(type: EloquentRelation::hasMany, related: 'App\Models\Post', foreignKey: 'user_id'),
            ]),
        ]
    );
    $errors = $relationshipData->validateImplementation($currentModels);
    expect($errors)->toContain('EloquentRelation App\Models\User::posts foreign key mismatch: expected user_id, got other_id');
});

test('it detects mismatched pivot table', function () {
    $currentModels = [
        'App\Models\User' => [
            'groups' => ['type' => EloquentRelation::belongsToMany, 'related' => 'App\Models\Group', 'pivotTable' => 'other_pivot'],
        ],
    ];
    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData(methods: [
                'groups' => new RelationData(type: EloquentRelation::belongsToMany, related: 'App\Models\Group', pivotTable: 'group_user'),
            ]),
        ]
    );
    $errors = $relationshipData->validateImplementation($currentModels);
    expect($errors)->toContain('EloquentRelation App\Models\User::groups pivot table mismatch: expected group_user, got other_pivot');
});

test('it detects related model not being an Eloquent model', function () {
    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData(methods: [
                'posts' => new RelationData(type: EloquentRelation::hasMany, related: \stdClass::class),
            ]),
        ]
    );
    $warnings = $relationshipData->getWarnings();
    expect($warnings)->toContain('Broken: App\Models\User::posts() points to class stdClass which is not an Eloquent Model.');
});

test('it detects missing ID warnings', function () {
    $relationshipData = new RelationshipData(config: new ConfigData, models: []);
    $warnings = $relationshipData->getWarnings(['users' => ['profile_id']]);
    expect($warnings)->toContain("Missing ID: users has column 'profile_id' but no matching relationship.");
});

test('it can generate diagram data', function () {
    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData(methods: [
                'posts' => new RelationData(type: EloquentRelation::hasMany, related: 'App\Models\Post'),
            ]),
            'App\Models\Post' => new ModelData(methods: [
                'user' => new RelationData(type: EloquentRelation::belongsTo, related: 'App\Models\User'),
            ]),
        ]
    );
    $diagramData = $relationshipData->getDiagramData();
    expect($diagramData)->toBeArray();
    $firstKey = array_key_first($diagramData);
    $relation = current($diagramData[$firstKey]);
    expect($relation)->toBe(EloquentRelation::bidirectional);
});

test('it handles symmetric belongsToMany without asymmetry warning', function () {
    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData(methods: [
                'groups' => new RelationData(type: EloquentRelation::belongsToMany, related: 'App\Models\Group'),
            ]),
            'App\Models\Group' => new ModelData(methods: [
                'users' => new RelationData(type: EloquentRelation::belongsToMany, related: 'App\Models\User'),
            ]),
        ]
    );
    $warnings = $relationshipData->getWarnings();

    expect($warnings)->not->toContain('Asymmetry: App\Models\User::groups() is belongsToMany but App\Models\Group has no inverse belongsToMany.');
});

test('getDiagramData handles special relations', function () {
    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData(methods: [
                'profile' => new RelationData(type: EloquentRelation::isSingle),
                'nothing' => new RelationData(type: EloquentRelation::noRelation),
            ]),
        ]
    );
    $diagramData = $relationshipData->getDiagramData();
    expect($diagramData)->toHaveKey('users');
});

test('getDiagramData handles pivot tables', function () {
    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData(methods: [
                'groups' => new RelationData(type: EloquentRelation::belongsToMany, related: 'App\Models\Group'),
            ]),
        ]
    );
    $diagramData = $relationshipData->getDiagramData(true);
    expect($diagramData)->toHaveKey('group_user');
});

test('it can save to file', function () {
    File::shouldReceive('put')->andReturn(true);
    $relationshipData = new RelationshipData(new ConfigData, []);
    expect($relationshipData->saveToFile('test.json'))->toBeTrue();
});

test('getWarnings handles belongsToMany without related model', function () {
    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData(methods: [
                'groups' => new RelationData(type: EloquentRelation::belongsToMany, related: null),
            ]),
        ]
    );
    $warnings = $relationshipData->getWarnings();
    expect($warnings)->toBeEmpty();
});

test('getWarnings handles broken relations without related model', function () {
    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData(methods: [
                'posts' => new RelationData(type: EloquentRelation::hasMany, related: null),
            ]),
        ]
    );
    $warnings = $relationshipData->getWarnings();
    expect($warnings)->toContain('Broken: App\Models\User::posts() has no related model defined.');
});

test('getDiagramData handles indirect and morph relations', function () {
    $relationshipData = new RelationshipData(
        config: new ConfigData,
        models: [
            'App\Models\User' => new ModelData(methods: [
                'indirect' => new RelationData(type: EloquentRelation::hasOneIndirect, related: 'App\Models\Other'),
                'morph' => new RelationData(type: EloquentRelation::morphToMany, related: 'App\Models\Tag'),
                'groups' => new RelationData(type: EloquentRelation::belongsToMany, related: 'App\Models\Alpha'), // Alpha < User (table name)
            ]),
        ]
    );
    $diagramData = $relationshipData->getDiagramData();
    expect($diagramData)->toHaveKey('users');
});
