<?php

namespace SchenkeIo\LaravelRelationManager\Phpunit;

use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Data\RelationData;
use SchenkeIo\LaravelRelationManager\Exceptions\LaravelNotLoadedException;

// https://github.com/AntonioPrimera/phpunit-custom-assertions/blob/master/src/Constraints/FoldersExistConstraint.php

class RelationshipExistsConstraint extends BaseConstraint
{
    /**
     * @param  RelationData  $other
     *
     * @throws LaravelNotLoadedException
     */
    protected function matches(mixed $other): bool
    {
        $this->expectation = ClassData::getRelationExpectation(
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
