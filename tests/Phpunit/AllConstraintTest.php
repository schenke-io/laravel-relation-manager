<?php

namespace SchenkeIo\LaravelRelationshipManager\Tests\Phpunit;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SchenkeIo\LaravelRelationshipManager\Data\ModelCountData;
use SchenkeIo\LaravelRelationshipManager\Data\RelationData;
use SchenkeIo\LaravelRelationshipManager\Phpunit\ClassAgeConstraint;
use SchenkeIo\LaravelRelationshipManager\Phpunit\ModelConstraint;
use SchenkeIo\LaravelRelationshipManager\Phpunit\NoRelationshipConstraint;
use SchenkeIo\LaravelRelationshipManager\Phpunit\RelationshipCountConstraint;
use SchenkeIo\LaravelRelationshipManager\Phpunit\RelationshipExistsConstraint;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Capital;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Country;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Single;
use SchenkeIo\LaravelRelationshipManager\Tests\TestCase;

class AllConstraintTest extends TestCase
{
    use RefreshDatabase;

    public function testClassAgeConstraint()
    {
        $classAgeConstraint = new ClassAgeConstraint();
        $this->assertThat(
            new RelationData(Country::class, Country::class),
            $classAgeConstraint
        );

        $this->assertThat(
            new RelationData(Country::class, ''),
            $this->logicalNot($classAgeConstraint)
        );
        $this->assertThat(
            new RelationData('', ''),
            $this->logicalNot($classAgeConstraint)
        );
        $this->assertIsString($classAgeConstraint->toString());
    }

    public function testModelConstraint()
    {
        $modelConstraint = new ModelConstraint();
        $this->assertThat(Country::class, $modelConstraint);
        $this->assertThat('', $this->logicalNot($modelConstraint));
        $this->assertIsString($modelConstraint->toString());
    }

    public function testNoRelationshipConstraint()
    {
        $noRelationshipConstraint = new NoRelationshipConstraint();
        $this->assertThat(Single::class, $noRelationshipConstraint);
        $this->assertIsString($noRelationshipConstraint->toString());
    }

    public function testRelationshipExistsConstraint()
    {
        $relationshipExistsConstraint = new RelationshipExistsConstraint();
        $this->assertThat(
            new RelationData(Country::class, Capital::class, HasOne::class),
            $relationshipExistsConstraint
        );
        $this->assertIsString($relationshipExistsConstraint->toString());
    }

    public function testRelationshipCountConstraint()
    {
        $relationshipCountConstraint = new RelationshipCountConstraint();
        $this->assertThat(
            new ModelCountData(Country::class, 3),
            $relationshipCountConstraint
        );
        $this->assertIsString($relationshipCountConstraint->toString());
    }
}
