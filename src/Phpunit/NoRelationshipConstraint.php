<?php

namespace SchenkeIo\LaravelRelationManager\Phpunit;

use SchenkeIo\LaravelRelationManager\Data\ClassData;

class NoRelationshipConstraint extends BaseConstraint
{
    /**
     * @param  string  $other Model name
     */
    protected function matches($other): bool
    {
        $relationshipCount = count(ClassData::take($other)->getModelRelations());
        $this->expectation = sprintf(
            'model %s has %d relations but expected 0',
            $other, $relationshipCount
        );

        return $relationshipCount == 0;
    }

    /**
     * Returns a string representation of the object.
     */
    public function toString(): string
    {
        return 'model has no relationships';
    }
}
