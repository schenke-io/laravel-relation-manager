<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Phpunit;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Phpunit\BaseConstraint;

class BaseConstraintTest extends TestCase
{
    public function test_base_constraint()
    {
        $baseConstraint = new class extends BaseConstraint
        {
            public function setExpectation($expectation): void
            {
                $this->expectation = $expectation;
            }

            public function failure($other): string
            {
                return $this->failureDescription($other);
            }
        };
        $text = '42342342424324';
        $baseConstraint->setExpectation($text);
        $this->assertEquals($baseConstraint->failure(null), $text);
        $this->assertIsString($baseConstraint->toString());
    }
}
