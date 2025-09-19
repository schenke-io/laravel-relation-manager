<?php

namespace SchenkeIo\LaravelRelationManager\Phpunit;

use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Data\ModelRelationData;
use SchenkeIo\LaravelRelationManager\Enums\Relation;
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
        if ($other->model2 === null) {
            return false;  // let it fail when not complete
        }
        if ($other->relation == Relation::morphedByMany) {
            // is visible from outside different
            $otherClass = Relation::morphToMany->getClass();
        } else {
            $otherClass = $other->relation->getClass();
        }
        if ($otherClass !== null) {
            $this->expectation = ClassData::getRelationExpectation(
                $other->model1,
                class_basename($otherClass),
                $other->model2
            );
        }

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
