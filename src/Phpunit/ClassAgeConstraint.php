<?php

namespace SchenkeIo\LaravelRelationManager\Phpunit;

use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Data\RelationData;

class ClassAgeConstraint extends BaseConstraint
{
    /**
     * @param  RelationData  $other
     */
    protected function matches($other): bool
    {
        $this->expectation = sprintf(
            'class %s is older than %s',
            $other->model1,
            $other->model2
        );

        return ClassData::take($other->model1)->isFresherOrEqualThan($other->model2);
    }

    /**
     * Returns a string representation of the object.
     */
    public function toString(): string
    {
        return 'time comparison of two class names';
    }
}
