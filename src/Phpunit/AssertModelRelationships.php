<?php

/**
 * ## possible assertions of Eloquent Models
 * add this file into your Phpunit test files
 *
 * ------
 *
 * This file is auto-generated by:
 * SchenkeIo\LaravelRelationManager\Writer\GenerateAssertModelRelationshipsTrait
 * using the data from: @see SchenkeIo\LaravelRelationManager\Define\RelationshipEnum
 */

namespace SchenkeIo\LaravelRelationManager\Phpunit;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use SchenkeIo\LaravelRelationManager\Data\ModelCountData;
use SchenkeIo\LaravelRelationManager\Data\RelationData;

trait AssertModelRelationships
{
    public function assertModelWorks(string $model): void
    {
        \PHPUnit\Framework\assertThat($model, new ModelConstraint());
    }

    public function assertFirstClassIsOlderThanSecondClass(string $class1, string $class2): void
    {
        \PHPUnit\Framework\assertThat(
            new RelationData($class1, $class2, ''),
            new ClassAgeConstraint()
        );
    }

    public function assertModelRelationCount(string $model, int $count): void
    {
        \PHPUnit\Framework\assertThat(
            new ModelCountData($model, $count),
            new RelationshipCountConstraint()
        );
    }

    public function assertModelHasOne(string $modelFrom, string $modelTo): void
    {
        \PHPUnit\Framework\assertThat(
            new RelationData($modelFrom, $modelTo, HasOne::class),
            new RelationshipExistsConstraint()
        );
    }

    public function assertModelHasMany(string $modelFrom, string $modelTo): void
    {
        \PHPUnit\Framework\assertThat(
            new RelationData($modelFrom, $modelTo, HasMany::class),
            new RelationshipExistsConstraint()
        );
    }

    public function assertModelHasOneThrough(string $modelFrom, string $modelTo): void
    {
        \PHPUnit\Framework\assertThat(
            new RelationData($modelFrom, $modelTo, HasOneThrough::class),
            new RelationshipExistsConstraint()
        );
    }

    public function assertModelHasManyThrough(string $modelFrom, string $modelTo): void
    {
        \PHPUnit\Framework\assertThat(
            new RelationData($modelFrom, $modelTo, HasManyThrough::class),
            new RelationshipExistsConstraint()
        );
    }

    public function assertModelBelongsToMany(string $modelFrom, string $modelTo): void
    {
        \PHPUnit\Framework\assertThat(
            new RelationData($modelFrom, $modelTo, BelongsToMany::class),
            new RelationshipExistsConstraint()
        );
    }

    public function assertModelBelongsTo(string $modelFrom, string $modelTo): void
    {
        \PHPUnit\Framework\assertThat(
            new RelationData($modelFrom, $modelTo, BelongsTo::class),
            new RelationshipExistsConstraint()
        );
    }

    public function assertModelIsSingle(string $model): void
    {
        \PHPUnit\Framework\assertThat($model, new NoRelationshipConstraint());
    }
}
