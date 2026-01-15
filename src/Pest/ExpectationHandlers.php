<?php

namespace SchenkeIo\LaravelRelationManager\Pest;

use PHPUnit\Framework\Assert;
use SchenkeIo\LaravelRelationManager\Data\ModelRelationData;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;
use SchenkeIo\LaravelRelationManager\Phpunit\RelationshipExistsConstraint;

/**
 * This class provides static handlers for custom Pest expectations.
 *
 * It facilitates the integration of Laravel Relation Manager assertions
 * into the Pest testing framework, allowing for expressive relationship testing.
 */
class ExpectationHandlers
{
    /**
     * @param  mixed  $expectation  The Pest expectation instance ($this)
     */
    public static function toHaveRelation(mixed $expectation, string $relatedModel, EloquentRelation $relation): mixed
    {
        Assert::assertThat(
            new ModelRelationData($expectation->value, $relatedModel, $relation),
            new RelationshipExistsConstraint
        );

        return $expectation;
    }
}
