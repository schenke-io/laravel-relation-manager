<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Phpunit;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SchenkeIo\LaravelRelationManager\Data\ModelCountData;
use SchenkeIo\LaravelRelationManager\Data\RelationData;
use SchenkeIo\LaravelRelationManager\Phpunit\ClassAgeConstraint;
use SchenkeIo\LaravelRelationManager\Phpunit\ModelConstraint;
use SchenkeIo\LaravelRelationManager\Phpunit\NoRelationshipConstraint;
use SchenkeIo\LaravelRelationManager\Phpunit\RelationshipCountConstraint;
use SchenkeIo\LaravelRelationManager\Phpunit\RelationshipExistsConstraint;
use SchenkeIo\LaravelRelationManager\Tests\database\Models\Capital;
use SchenkeIo\LaravelRelationManager\Tests\database\Models\Country;
use SchenkeIo\LaravelRelationManager\Tests\database\Models\Single;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

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
