<?php

namespace SchenkeIo\LaravelRelationshipManager\Phpunit;

use SchenkeIo\LaravelRelationshipManager\Data\ClassData;

class ModelConstraint extends BaseConstraint
{
    /**
     * @param  string  $other
     */
    protected function matches($other): bool
    {
        $this->expectation = "$other is a model";

        return ClassData::take($other)->isModel;
    }

    /**
     * Returns a string representation of the object.
     */
    public function toString(): string
    {
        return 'correct definition of a model';
    }
}
