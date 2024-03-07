<?php

namespace SchenkeIo\LaravelRelationManager\Phpunit;

use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Data\ModelRelationData;
use SchenkeIo\LaravelRelationManager\Exceptions\LaravelNotLoadedException;

// https://github.com/AntonioPrimera/phpunit-custom-assertions/blob/master/src/Constraints/FoldersExistConstraint.php

class RelationshipExistsConstraint extends BaseConstraint
{
    /**
     * @param  ModelRelationData  $other
     *
     * @throws LaravelNotLoadedException
     * @throws \Exception
     */
    protected function matches(mixed $other): bool
    {
        $this->expectation = ClassData::getRelationExpectation(
            $other->model1,
            class_basename($other->relation->getClass()),
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
