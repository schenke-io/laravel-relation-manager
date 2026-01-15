<?php

use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;

test('it can map from relation name', function () {
    expect(EloquentRelation::fromRelationName('BelongsTo'))->toBe(EloquentRelation::belongsTo)
        ->and(EloquentRelation::fromRelationName('BelongsToMany'))->toBe(EloquentRelation::belongsToMany)
        ->and(EloquentRelation::fromRelationName('HasMany'))->toBe(EloquentRelation::hasMany)
        ->and(EloquentRelation::fromRelationName('HasManyThrough'))->toBe(EloquentRelation::hasManyThrough)
        ->and(EloquentRelation::fromRelationName('HasOne'))->toBe(EloquentRelation::hasOne)
        ->and(EloquentRelation::fromRelationName('HasOneThrough'))->toBe(EloquentRelation::hasOneThrough)
        ->and(EloquentRelation::fromRelationName('MorphMany'))->toBe(EloquentRelation::morphMany)
        ->and(EloquentRelation::fromRelationName('MorphOne'))->toBe(EloquentRelation::morphOne)
        ->and(EloquentRelation::fromRelationName('MorphTo'))->toBe(EloquentRelation::morphTo)
        ->and(EloquentRelation::fromRelationName('MorphToMany'))->toBe(EloquentRelation::morphToMany)
        ->and(EloquentRelation::fromRelationName('Unknown'))->toBe(EloquentRelation::noRelation);
});

test('it returns assert name', function () {
    expect(EloquentRelation::hasOne->getAssertName())->toBe('assertModelHasOne');
});

test('it knows when to ask for inverse', function () {
    expect(EloquentRelation::hasOne->askForInverse())->toBeTrue()
        ->and(EloquentRelation::noRelation->askForInverse())->toBeFalse();
});

test('it knows when to ask for related model', function () {
    expect(EloquentRelation::hasOne->askForRelatedModel())->toBeTrue()
        ->and(EloquentRelation::morphTo->askForRelatedModel())->toBeFalse();
});

test('it returns inverse', function () {
    expect(EloquentRelation::hasOne->inverse())->toBe(EloquentRelation::belongsTo)
        ->and(EloquentRelation::belongsToMany->inverse())->toBe(EloquentRelation::belongsToMany)
        ->and(EloquentRelation::morphOne->inverse())->toBe(EloquentRelation::morphTo)
        ->and(EloquentRelation::morphToMany->inverse())->toBe(EloquentRelation::morphedByMany)
        ->and(EloquentRelation::noRelation->inverse())->toBe(EloquentRelation::noRelation)
        ->and(EloquentRelation::hasOne->inverse(true))->toBe(EloquentRelation::noRelation);
});

test('it has inverse', function () {
    expect(EloquentRelation::hasOne->hasInverse())->toBeTrue()
        ->and(EloquentRelation::noRelation->hasInverse())->toBeFalse();
});

test('it knows if it has public function', function () {
    expect(EloquentRelation::hasOne->hasPublicFunction())->toBeTrue()
        ->and(EloquentRelation::belongsTo->hasPublicFunction())->toBeFalse();
});

test('it knows if it is a relation', function () {
    expect(EloquentRelation::hasOne->isRelation())->toBeTrue()
        ->and(EloquentRelation::noRelation->isRelation())->toBeFalse();
});

test('it knows if it is a direct relation', function () {
    expect(EloquentRelation::hasOne->isDirectRelation())->toBeTrue()
        ->and(EloquentRelation::hasOneThrough->isDirectRelation())->toBeFalse();
});

test('it returns class', function () {
    expect(EloquentRelation::hasOne->getClass())->toBe(\Illuminate\Database\Eloquent\Relations\HasOne::class)
        ->and(EloquentRelation::noRelation->getClass())->toBeNull();
});

test('it knows if it is morph', function () {
    expect(EloquentRelation::morphOne->isMorph())->toBeTrue()
        ->and(EloquentRelation::hasOne->isMorph())->toBeFalse();
});

test('it knows if it is double line', function () {
    expect(EloquentRelation::belongsToMany->isDoubleLine())->toBeTrue()
        ->and(EloquentRelation::hasOne->isDoubleLine())->toBeFalse();
});

test('it can be created from name via EnumHelper', function () {
    expect(EloquentRelation::from('hasOne'))->toBe(EloquentRelation::hasOne);
});

test('it returns null for invalid name via EnumHelper tryFrom', function () {
    expect(EloquentRelation::tryFrom('XX'))->toBeNull();
});

test('it throws error for invalid name via EnumHelper from', function () {
    EloquentRelation::from('XX');
})->throws(\ValueError::class);
