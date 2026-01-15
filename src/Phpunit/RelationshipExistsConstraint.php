<?php

namespace SchenkeIo\LaravelRelationManager\Phpunit;

use PHPUnit\Framework\Constraint\Constraint;
use SchenkeIo\LaravelRelationManager\Data\ModelRelationData;
use SchenkeIo\LaravelRelationManager\Facades\ModelScanner;

/**
 * PHPUnit constraint to verify that a specific relationship exists on a model.
 */
class RelationshipExistsConstraint extends Constraint
{
    protected string $error = '';

    protected function matches(mixed $other): bool
    {
        if (! ($other instanceof ModelRelationData)) {
            return false;
        }

        $models = ModelScanner::scan();
        $model1 = $other->model1;
        $model2 = $other->model2;
        $relation = $other->relation;

        if (! isset($models[$model1])) {
            $this->error = "Model $model1 not found";

            return false;
        }

        foreach ($models[$model1] as $relName => $relData) {
            if ($relData['type'] === $relation && $relData['related'] === $model2) {
                return true;
            }
        }

        $this->error = "Relationship $relation->name with $model2 not found in $model1";

        return false;
    }

    public function toString(): string
    {
        return $this->error;
    }
}
