<?php

namespace SchenkeIo\LaravelRelationManager\Phpunit;

use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Data\RelationData;

class ClassAgeConstraint extends BaseConstraint
{
    /**
     * @param  RelationData  $other
     */
    protected function matches(mixed $other): bool
    {
        $this->expectation = sprintf(
            'class %s is older than %s',
            $other->model1,  // old
            $other->model2   // new
        );

        return ClassData::take($other->model2)->isFresherOrEqualThan($other->model1);
    }

    /**
     * Returns a string representation of the object.
     */
    public function toString(): string
    {
        return 'time comparison of two class names';
    }
}
