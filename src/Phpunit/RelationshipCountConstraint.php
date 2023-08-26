<?php

namespace SchenkeIo\LaravelRelationshipManager\Phpunit;

use SchenkeIo\LaravelRelationshipManager\Data\ClassData;
use SchenkeIo\LaravelRelationshipManager\Data\ModelCountData;

// https://github.com/AntonioPrimera/phpunit-custom-assertions/blob/master/src/Constraints/FoldersExistConstraint.php

class RelationshipCountConstraint extends BaseConstraint
{
    /**
     * @param  ModelCountData  $other
     */
    protected function matches($other): bool
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
