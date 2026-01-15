<?php

use SchenkeIo\LaravelRelationManager\Enums\Relation;

test('it can map from relation name', function () {
    expect(Relation::fromRelationName('BelongsTo'))->toBe(Relation::belongsTo)
        ->and(Relation::fromRelationName('BelongsToMany'))->toBe(Relation::belongsToMany)
        ->and(Relation::fromRelationName('HasMany'))->toBe(Relation::hasMany)
        ->and(Relation::fromRelationName('HasManyThrough'))->toBe(Relation::hasManyThrough)
        ->and(Relation::fromRelationName('HasOne'))->toBe(Relation::hasOne)
        ->and(Relation::fromRelationName('HasOneThrough'))->toBe(Relation::hasOneThrough)
        ->and(Relation::fromRelationName('MorphMany'))->toBe(Relation::morphMany)
        ->and(Relation::fromRelationName('MorphOne'))->toBe(Relation::morphOne)
        ->and(Relation::fromRelationName('MorphTo'))->toBe(Relation::morphTo)
        ->and(Relation::fromRelationName('MorphToMany'))->toBe(Relation::morphToMany)
        ->and(Relation::fromRelationName('Unknown'))->toBe(Relation::noRelation);
});

test('it returns assert name', function () {
    expect(Relation::hasOne->getAssertName())->toBe('assertModelHasOne');
});

test('it knows when to ask for inverse', function () {
    expect(Relation::hasOne->askForInverse())->toBeTrue()
        ->and(Relation::noRelation->askForInverse())->toBeFalse();
});

test('it knows when to ask for related model', function () {
    expect(Relation::hasOne->askForRelatedModel())->toBeTrue()
        ->and(Relation::morphTo->askForRelatedModel())->toBeFalse();
});

test('it returns inverse', function () {
    expect(Relation::hasOne->inverse())->toBe(Relation::belongsTo)
        ->and(Relation::belongsToMany->inverse())->toBe(Relation::belongsToMany)
        ->and(Relation::morphOne->inverse())->toBe(Relation::morphTo)
        ->and(Relation::morphToMany->inverse())->toBe(Relation::morphedByMany)
        ->and(Relation::noRelation->inverse())->toBe(Relation::noRelation)
        ->and(Relation::hasOne->inverse(true))->toBe(Relation::noRelation);
});

test('it has inverse', function () {
    expect(Relation::hasOne->hasInverse())->toBeTrue()
        ->and(Relation::noRelation->hasInverse())->toBeFalse();
});

test('it knows if it has public function', function () {
    expect(Relation::hasOne->hasPublicFunction())->toBeTrue()
        ->and(Relation::belongsTo->hasPublicFunction())->toBeFalse();
});

test('it knows if it is a relation', function () {
    expect(Relation::hasOne->isRelation())->toBeTrue()
        ->and(Relation::noRelation->isRelation())->toBeFalse();
});

test('it knows if it is a direct relation', function () {
    expect(Relation::hasOne->isDirectRelation())->toBeTrue()
        ->and(Relation::hasOneThrough->isDirectRelation())->toBeFalse();
});

test('it returns class', function () {
    expect(Relation::hasOne->getClass())->toBe(\Illuminate\Database\Eloquent\Relations\HasOne::class)
        ->and(Relation::noRelation->getClass())->toBeNull();
});

test('it knows if it is morph', function () {
    expect(Relation::morphOne->isMorph())->toBeTrue()
        ->and(Relation::hasOne->isMorph())->toBeFalse();
});

test('it knows if it is double line', function () {
    expect(Relation::belongsToMany->isDoubleLine())->toBeTrue()
        ->and(Relation::hasOne->isDoubleLine())->toBeFalse();
});

test('it can be created from name via EnumHelper', function () {
    expect(Relation::from('hasOne'))->toBe(Relation::hasOne);
});

test('it returns null for invalid name via EnumHelper tryFrom', function () {
    expect(Relation::tryFrom('XX'))->toBeNull();
});

test('it throws error for invalid name via EnumHelper from', function () {
    Relation::from('XX');
})->throws(\ValueError::class);
