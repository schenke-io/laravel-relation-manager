<?php

namespace SchenkeIo\LaravelRelationManager\Phpunit;

use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Assert;
use SchenkeIo\LaravelRelationManager\Data\ModelRelationData;
use SchenkeIo\LaravelRelationManager\Data\RelationshipData;
use SchenkeIo\LaravelRelationManager\Enums\Relation;
use SchenkeIo\LaravelRelationManager\Facades\ModelScanner;
use SchenkeIo\LaravelRelationManager\Support\PathResolver;

/**
 * Trait containing the core logic for relationship tests.
 * This can be used by both PHPUnit and Pest.
 */
trait RelationTestTrait
{
    /**
     * Asserts that the Laravel application is properly booted.
     */
    public function assertLaravelEnvironment(): void
    {
        Assert::assertTrue(function_exists('app'), 'Laravel is not booted. Ensure this test runs within a Laravel environment.');
    }

    /**
     * Asserts that the .relationships.json file exists and can be parsed.
     */
    public function assertRelationshipJsonExistsAndIsValid(?string $relationshipJsonPath = null): void
    {
        $path = $relationshipJsonPath ? base_path($relationshipJsonPath) : PathResolver::getRelationshipFilePath();
        Assert::assertTrue(File::exists($path), "Relationship JSON file not found at: $path");
        $relationshipData = RelationshipData::loadFromFile($path);
        Assert::assertNotNull($relationshipData, 'Relationship JSON file is not valid');
    }

    /**
     * Asserts that the current model implementation matches the state saved in JSON.
     *
     * @param  string|null  $relationshipJsonPath  Path to the JSON file.
     * @param  string|null  $modelDirectory  Directory containing the models to scan.
     * @param  bool  $strict  If true, ensures no extra relations exist in the models.
     */
    public function assertModelsMatchJsonState(
        ?string $relationshipJsonPath = null,
        ?string $modelDirectory = null,
        bool $strict = false
    ): void {
        $path = $relationshipJsonPath ? base_path($relationshipJsonPath) : PathResolver::getRelationshipFilePath();
        $currentModels = ModelScanner::scan($modelDirectory);
        $relationshipData = RelationshipData::loadFromFile($path);
        Assert::assertNotNull($relationshipData, 'Relationship JSON file is not valid');

        $errors = $relationshipData->validateImplementation($currentModels, $strict);

        Assert::assertEmpty($errors, "Relationship validation failed:\n".implode("\n", $errors));
    }

    public function assertModelHasOne(string $model, string $relatedModel): void
    {
        $this->assertModelHasRelation($model, $relatedModel, Relation::hasOne);
    }

    public function assertModelHasMany(string $model, string $relatedModel): void
    {
        $this->assertModelHasRelation($model, $relatedModel, Relation::hasMany);
    }

    public function assertModelBelongsTo(string $model, string $relatedModel): void
    {
        $this->assertModelHasRelation($model, $relatedModel, Relation::belongsTo);
    }

    public function assertModelBelongsToMany(string $model, string $relatedModel): void
    {
        $this->assertModelHasRelation($model, $relatedModel, Relation::belongsToMany);
    }

    /**
     * Asserts that a specific relationship exists between two models.
     */
    public function assertModelHasRelation(string $model, string $relatedModel, Relation $relation): void
    {
        Assert::assertThat(
            new ModelRelationData($model, $relatedModel, $relation),
            new RelationshipExistsConstraint
        );
    }
}
