<?php

namespace SchenkeIo\LaravelRelationManager\Pest;

use SchenkeIo\LaravelRelationManager\Phpunit\RelationTestTrait;

/**
 * Bridge class to allow AbstractRelationTest usage in Pest.
 */
class RelationTestBridge
{
    use RelationTestTrait;

    /**
     * Registers all standard relationship tests in the current Pest file.
     *
     * @param  string  $relationshipJsonPath  Path to the relationship JSON file.
     * @param  string|null  $modelDirectory  Directory containing the models.
     * @param  bool  $strict  Whether to use strict validation.
     */
    public static function all(
        ?string $relationshipJsonPath = null,
        ?string $modelDirectory = null,
        bool $strict = false
    ): void {
        $bridge = new self;

        test('laravel environment', fn () => $bridge->assertLaravelEnvironment());

        test('relationship json exists and is valid', fn () => $bridge->assertRelationshipJsonExistsAndIsValid($relationshipJsonPath));

        test('models match json state', fn () => $bridge->assertModelsMatchJsonState($relationshipJsonPath, $modelDirectory, $strict));
    }
}
