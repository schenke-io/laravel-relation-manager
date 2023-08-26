<?php

namespace SchenkeIo\LaravelRelationshipManager\Phpunit;

use PHPUnit\Framework\Constraint\Constraint;

class BaseConstraint extends Constraint
{
    protected string $expectation = '';

    protected function failureDescription(mixed $other): string
    {
        return $this->expectation;
    }

    /**
     * Returns a string representation of the object.
     */
    public function toString(): string
    {
        return __CLASS__;
    }
}
