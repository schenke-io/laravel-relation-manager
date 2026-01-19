<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use Illuminate\Database\Eloquent\Model;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;

/**
 * Service to validate the relationship data against the implementation.
 *
 * It identifies inconsistencies between the defined relationship JSON
 * and the actual Eloquent model implementation.
 */
class RelationshipValidator
{
    /**
     * @param  array<string, ModelData>  $models
     */
    public function __construct(protected array $models) {}

    /**
     * Validates that the current model implementation matches the stored relationship data.
     *
     * @param  array<string, array<string, array<string, mixed>>>  $currentModels
     * @return array<string> List of error messages.
     */
    public function validateImplementation(array $currentModels, bool $strict = false): array
    {
        $errors = [];

        if ($strict) {
            foreach ($currentModels as $model => $relations) {
                if (! isset($this->models[$model])) {
                    $errors[] = "Model $model found in implementation but missing in JSON";

                    continue;
                }
                foreach ($relations as $method => $data) {
                    if (! isset($this->models[$model]->methods[$method])) {
                        $errors[] = "EloquentRelation $model::$method found in implementation but missing in JSON";
                    }
                }
            }
        }

        foreach ($this->models as $model => $modelData) {
            if (! isset($currentModels[$model])) {
                $errors[] = "Model $model missing in implementation";

                continue;
            }

            foreach ($modelData->methods as $method => $expectedData) {
                if (! isset($currentModels[$model][$method])) {
                    $errors[] = "EloquentRelation $model::$method missing in implementation";

                    continue;
                }

                $currentData = $currentModels[$model][$method];

                // Compare type
                $expectedType = $expectedData->type->name;
                $currentType = ($currentData['type'] instanceof EloquentRelation) ? $currentData['type']->name : $currentData['type'];

                if ($currentType !== $expectedType) {
                    $errors[] = "EloquentRelation $model::$method type mismatch: expected $expectedType, got $currentType";
                }

                // Compare related model
                $expectedRelated = $expectedData->related;
                $currentRelated = $currentData['related'] ?? null;

                if ($expectedRelated && $currentRelated !== $expectedRelated) {
                    $errors[] = "EloquentRelation $model::$method related model mismatch: expected $expectedRelated, got $currentRelated";
                }

                // Compare foreign key
                $expectedForeignKey = $expectedData->foreignKey;
                $currentForeignKey = $currentData['foreignKey'] ?? null;
                if ($expectedForeignKey && $currentForeignKey !== $expectedForeignKey) {
                    $errors[] = "EloquentRelation $model::$method foreign key mismatch: expected $expectedForeignKey, got $currentForeignKey";
                }

                // Compare pivot table
                $expectedPivotTable = $expectedData->pivotTable;
                $currentPivotTable = $currentData['pivotTable'] ?? null;
                if ($expectedPivotTable && $currentPivotTable !== $expectedPivotTable) {
                    $errors[] = "EloquentRelation $model::$method pivot table mismatch: expected $expectedPivotTable, got $currentPivotTable";
                }
            }
        }

        return $errors;
    }

    /**
     * Checks for potential issues in the relationship data.
     *
     * @param  array<string, string[]>  $potentialRelations
     * @return string[] List of warning messages.
     */
    public function getWarnings(array $potentialRelations = []): array
    {
        $warnings = [];

        foreach ($this->models as $model => $modelData) {
            foreach ($modelData->methods as $method => $data) {
                $typeEnum = $data->type;

                // Asymmetry check
                if ($typeEnum === EloquentRelation::belongsToMany) {
                    $relatedModel = $data->related;
                    if (! $relatedModel) {
                        continue;
                    }

                    $foundInverse = false;
                    if (isset($this->models[$relatedModel])) {
                        foreach ($this->models[$relatedModel]->methods as $inverseData) {
                            $inverseType = $inverseData->type;
                            if ($inverseType === EloquentRelation::belongsToMany && $inverseData->related === $model) {
                                $foundInverse = true;
                                break;
                            }
                        }
                    }

                    if (! $foundInverse) {
                        $warnings[] = "Asymmetry: $model::$method() is belongsToMany but $relatedModel has no inverse belongsToMany.";
                    }
                }

                // Broken relations check
                $related = $data->related;
                if (! $related) {
                    if ($typeEnum !== EloquentRelation::morphTo && $typeEnum !== EloquentRelation::noRelation) {
                        $warnings[] = "Broken: $model::$method() has no related model defined.";
                    }

                    continue;
                }

                if (! class_exists($related)) {
                    $warnings[] = "Broken: $model::$method() points to non-existent class $related.";
                } elseif (! is_subclass_of($related, Model::class)) {
                    $warnings[] = "Broken: $model::$method() points to class $related which is not an Eloquent Model.";
                }
            }
        }

        // Missing ID check
        foreach ($potentialRelations as $model => $columns) {
            foreach ($columns as $column) {
                $warnings[] = "Missing ID: $model has column '$column' but no matching relationship.";
            }
        }

        return $warnings;
    }
}
