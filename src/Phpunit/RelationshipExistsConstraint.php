<?php

namespace SchenkeIo\LaravelRelationshipManager\Phpunit;

use SchenkeIo\LaravelRelationshipManager\Data\ClassData;
use SchenkeIo\LaravelRelationshipManager\Data\RelationData;

// https://github.com/AntonioPrimera/phpunit-custom-assertions/blob/master/src/Constraints/FoldersExistConstraint.php

class RelationshipExistsConstraint extends BaseConstraint
{
    /**
     * @param  RelationData  $other
     */
    protected function matches($other): bool
    {
        $this->expectation = ClassData::getRelationNotFoundError(
            $other->model1,
            $other->relationship,
            $other->model2
        );

        return $this->expectation == '';
    }

    /**
     * Returns a string representation of the object.
     */
    public function toString(): string
    {
        return 'correct definition of a relationship';
    }
}
