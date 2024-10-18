<?php

namespace SchenkeIo\LaravelRelationManager\Phpunit;

use SchenkeIo\LaravelRelationManager\Data\ClassData;

class ModelConstraint extends BaseConstraint
{
    /**
     * @param  string  $other
     */
    protected function matches(mixed $other): bool
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
