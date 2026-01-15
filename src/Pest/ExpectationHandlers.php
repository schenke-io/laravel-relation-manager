<?php

namespace SchenkeIo\LaravelRelationManager\Pest;

use PHPUnit\Framework\Assert;
use SchenkeIo\LaravelRelationManager\Data\ModelRelationData;
use SchenkeIo\LaravelRelationManager\Enums\Relation;
use SchenkeIo\LaravelRelationManager\Phpunit\RelationshipExistsConstraint;

class ExpectationHandlers
{
    /**
     * @param  mixed  $expectation  The Pest expectation instance ($this)
     */
    public static function toHaveRelation(mixed $expectation, string $relatedModel, Relation $relation): mixed
    {
        Assert::assertThat(
            new ModelRelationData($expectation->value, $relatedModel, $relation),
            new RelationshipExistsConstraint
        );

        return $expectation;
    }
}
