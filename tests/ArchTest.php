<?php

use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Define\RelationTypes;
use SchenkeIo\LaravelRelationManager\Enums\Relation;
use SchenkeIo\LaravelRelationManager\Phpunit\AssertModelRelations;

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

it('can verify if AssertModelRelations is outdated', function () {
    expect(
        ClassData::take(AssertModelRelations::class)
            ->isFresherOrEqualThan(RelationTypes::class)
    )->toBeTrue();
});

it('can verify if RelationTypes is outdated', function () {
    expect(
        ClassData::take(RelationTypes::class)
            ->isFresherOrEqualThan(Relation::class)
    )->toBeTrue();
});
