<?php

namespace SchenkeIo\LaravelRelationManager\Phpunit;

use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Data\ModelCountData;
use SchenkeIo\LaravelRelationManager\Exceptions\LaravelNotLoadedException;

// https://github.com/AntonioPrimera/phpunit-custom-assertions/blob/master/src/Constraints/FoldersExistConstraint.php

class RelationshipCountConstraint extends BaseConstraint
{
    /**
     * @param  ModelCountData  $other
     *
     * @throws LaravelNotLoadedException
     * @throws \ReflectionException
     */
    protected function matches(mixed $other): bool
    {
        $count = ClassData::getRelationCountOfModel($other->model);
        $this->expectation = sprintf(
            'model %s should have %d relations but found %d',
            $other->model, $other->count, $count
        );

        return $count === $other->count;
    }

    /**
     * Returns a string representation of the object.
     */
    public function toString(): string
    {
        return 'count of Eloquent relations';
    }
}
