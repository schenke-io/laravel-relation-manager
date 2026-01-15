<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Phpunit;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Phpunit\RelationshipExistsConstraint;

class RelationshipExistsConstraintTest extends TestCase
{
    public function test_matches_returns_false_for_invalid_input()
    {
        $constraint = new RelationshipExistsConstraint;
        // matches is protected, but evaluate calls it
        $this->assertFalse($constraint->evaluate('not a ModelRelationData', '', true));
    }
}
