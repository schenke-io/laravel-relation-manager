<?php

namespace SchenkeIo\LaravelRelationManager\Phpunit;

use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Exceptions\LaravelNotLoadedException;

class NoRelationshipConstraint extends BaseConstraint
{
    /**
     * @param  string  $other  Model name
     *
     * @throws LaravelNotLoadedException
     */
    protected function matches(mixed $other): bool
    {
        $count = ClassData::getRelationCountOfModel($other);
        $this->expectation = sprintf(
            'model %s has %d relations but expected 0',
            $other, $count
        );

        return $count == 0;
    }

    /**
     * Returns a string representation of the object.
     */
    public function toString(): string
    {
        return 'model has no relationships';
    }
}
