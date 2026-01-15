<?php

namespace SchenkeIo\LaravelRelationManager\Phpunit;

use PHPUnit\Framework\TestCase;

/**
 * Abstract base class for relationship tests using PHPUnit.
 * Provides standard tests to verify the existence and validity
 * of the relationship JSON file and its sync with model implementation.
 */
abstract class AbstractRelationTest extends TestCase
{
    use RelationTestTrait;

    protected ?string $relationshipJsonPath = null;

    protected ?string $modelDirectory = null;

    protected bool $strict = false;

    public function test_laravel_environment(): void
    {
        $this->assertLaravelEnvironment();
    }

    public function test_relationship_json_exists_and_is_valid(): void
    {
        $this->assertRelationshipJsonExistsAndIsValid($this->relationshipJsonPath);
    }

    public function test_models_match_json_state(): void
    {
        $this->assertModelsMatchJsonState($this->relationshipJsonPath, $this->modelDirectory, $this->strict);
    }
}
