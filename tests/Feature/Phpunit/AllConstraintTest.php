<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Phpunit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use SchenkeIo\LaravelRelationManager\Data\ModelCountData;
use SchenkeIo\LaravelRelationManager\Data\ModelRelationData;
use SchenkeIo\LaravelRelationManager\Define\RelationsEnum;
use SchenkeIo\LaravelRelationManager\Phpunit\ClassAgeConstraint;
use SchenkeIo\LaravelRelationManager\Phpunit\ModelBackedEnumConstraint;
use SchenkeIo\LaravelRelationManager\Phpunit\NoRelationshipConstraint;
use SchenkeIo\LaravelRelationManager\Phpunit\RelationshipCountConstraint;
use SchenkeIo\LaravelRelationManager\Phpunit\RelationshipExistsConstraint;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use Workbench\App\Models\Capital;
use Workbench\App\Models\Country;
use Workbench\App\Models\Single;

class AllConstraintTest extends TestCase
{
    use RefreshDatabase;

    public function testClassAgeConstraint()
    {
        $classAgeConstraint = new ClassAgeConstraint;
        $this->assertThat(
            new ModelRelationData(Country::class, Country::class),
            $classAgeConstraint
        );

        $this->assertThat(
            new ModelRelationData(Country::class, ''),
            $this->logicalNot($classAgeConstraint)
        );
        $this->assertThat(
            new ModelRelationData('', ''),
            $this->logicalNot($classAgeConstraint)
        );
        $this->assertIsString($classAgeConstraint->toString());
    }

    public function testModelConstraint()
    {
        $modelConstraint = new ModelBackedEnumConstraint;
        $this->assertThat(Country::class, $modelConstraint);
        $this->assertThat('', $this->logicalNot($modelConstraint));
        $this->assertIsString($modelConstraint->toString());
    }

    public function testNoRelationshipConstraint()
    {
        $noRelationshipConstraint = new NoRelationshipConstraint;
        $this->assertThat(Single::class, $noRelationshipConstraint);
        $this->assertIsString($noRelationshipConstraint->toString());
    }

    public function testRelationshipExistsConstraint()
    {
        $relationshipExistsConstraint = new RelationshipExistsConstraint;
        $this->assertThat(
            new ModelRelationData(Country::class, Capital::class, RelationsEnum::hasOne),
            $relationshipExistsConstraint
        );
        $this->assertIsString($relationshipExistsConstraint->toString());
    }

    public function testRelationshipCountConstraint()
    {
        $relationshipCountConstraint = new RelationshipCountConstraint;
        $this->assertThat(
            new ModelCountData(Country::class, 3),
            $relationshipCountConstraint
        );
        $this->assertIsString($relationshipCountConstraint->toString());
    }
}
