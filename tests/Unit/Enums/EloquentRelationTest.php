<?php

use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;

it('maps relation names to enum cases', function () {
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

it('can get assert name', function () {
    expect(EloquentRelation::hasOne->getAssertName())->toBe('assertModelHasOne');
});

it('knows when to ask for inverse', function () {
    expect(EloquentRelation::hasOne->askForInverse())->toBeTrue()
        ->and(EloquentRelation::noRelation->askForInverse())->toBeFalse();
});

it('knows when to ask for related model', function () {
    expect(EloquentRelation::hasMany->askForRelatedModel())->toBeTrue()
        ->and(EloquentRelation::isSingle->askForRelatedModel())->toBeFalse();
});

it('can get inverse', function () {
    expect(EloquentRelation::hasOne->inverse())->toBe(EloquentRelation::belongsTo)
        ->and(EloquentRelation::hasMany->inverse())->toBe(EloquentRelation::belongsTo)
        ->and(EloquentRelation::belongsTo->inverse())->toBe(EloquentRelation::hasMany)
        ->and(EloquentRelation::belongsToMany->inverse())->toBe(EloquentRelation::belongsToMany)
        ->and(EloquentRelation::morphOne->inverse())->toBe(EloquentRelation::morphTo)
        ->and(EloquentRelation::morphMany->inverse())->toBe(EloquentRelation::morphTo)
        ->and(EloquentRelation::morphTo->inverse())->toBe(EloquentRelation::morphMany)
        ->and(EloquentRelation::morphToMany->inverse())->toBe(EloquentRelation::morphedByMany)
        ->and(EloquentRelation::morphedByMany->inverse())->toBe(EloquentRelation::morphToMany)
        ->and(EloquentRelation::noRelation->inverse())->toBe(EloquentRelation::noRelation)
        ->and(EloquentRelation::hasOne->inverse(true))->toBe(EloquentRelation::noRelation);
});

it('can check if it has inverse', function () {
    expect(EloquentRelation::hasOne->hasInverse())->toBeTrue()
        ->and(EloquentRelation::noRelation->hasInverse())->toBeFalse();
});

it('knows if it has a public function', function () {
    expect(EloquentRelation::hasMany->hasPublicFunction())->toBeTrue()
        ->and(EloquentRelation::belongsTo->hasPublicFunction())->toBeFalse();
});

it('knows if it is a relation', function () {
    expect(EloquentRelation::hasMany->isRelation())->toBeTrue()
        ->and(EloquentRelation::isSingle->isRelation())->toBeFalse();
});

it('knows if it is a direct relation', function () {
    expect(EloquentRelation::hasMany->isDirectRelation())->toBeTrue()
        ->and(EloquentRelation::hasManyThrough->isDirectRelation())->toBeFalse();
});

it('can get relation class', function () {
    expect(EloquentRelation::hasMany->getClass())->toBe(\Illuminate\Database\Eloquent\Relations\HasMany::class)
        ->and(EloquentRelation::noRelation->getClass())->toBeNull();
});

it('knows if it is morph', function () {
    expect(EloquentRelation::morphOne->isMorph())->toBeTrue()
        ->and(EloquentRelation::hasMany->isMorph())->toBeFalse();
});

it('knows if it is double line', function () {
    expect(EloquentRelation::belongsToMany->isDoubleLine())->toBeTrue()
        ->and(EloquentRelation::hasMany->isDoubleLine())->toBeFalse();
});
