<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * Data Transfer Object for all relationship data of the project.
 *
 * This class serves as the central repository for relationship metadata
 * extracted from Eloquent models. It includes configuration settings and
 * a collection of model-specific relationship data. It provides methods
 * for loading from and saving to JSON files, validating the current
 * implementation against the stored data, generating warnings for
 * inconsistencies, and preparing data for visualization (diagrams).
 */
#[MapName(SnakeCaseMapper::class)]
class RelationshipData extends Data
{
    public function __construct(
        public readonly ConfigData $config = new ConfigData,
        /** @var array<string, ModelData> */
        public readonly array $models = [],
    ) {}

    /**
     * Custom instantiation logic to handle older data formats or nested arrays.
     *
     * @param  mixed  ...$payloads  Payloads to create the instance from.
     */
    public static function from(mixed ...$payloads): static
    {
        $data = $payloads[0] ?? [];
        if (is_array($data)) {
            if (isset($data['models']) && is_array($data['models'])) {
                foreach ($data['models'] as $modelName => $modelData) {
                    if (is_array($modelData) && ! isset($modelData['methods'])) {
                        $data['models'][$modelName] = [
                            'methods' => $modelData,
                        ];
                    }
                }
            }
            if (! isset($data['config'])) {
                $data['config'] = [];
            }
        }

        return parent::from($data);
    }

    /**
     * Loads relationship data from a JSON file.
     *
     * @param  string  $path  The full path to the JSON file.
     * @return self|null The loaded data or null if the file doesn't exist or is invalid.
     */
    public static function loadFromFile(string $path): ?self
    {
        if (! File::exists($path)) {
            return null;
        }

        $content = File::get($path);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($data)) {
            return null;
        }

        return self::from($data);
    }

    /**
     * Persists the current relationship data to a JSON file.
     *
     * @param  string  $path  The full path where the JSON should be saved.
     * @return bool True on success, false otherwise.
     */
    public function saveToFile(string $path): bool
    {
        $json = $this->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return (bool) File::put($path, $json);
    }

    /**
     * Validates that the current model implementation matches the stored relationship data.
     *
     * It compares types, related models, foreign keys, and pivot tables.
     *
     * @param  array<string, array<string, array<string, mixed>>>  $currentModels  The implementation data discovered by scanning.
     * @param  bool  $strict  If true, it also warns about items in implementation missing in JSON.
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
     * It identifies:
     * 1. Asymmetry (e.g., belongsToMany without an inverse).
     * 2. Broken relations (missing related model or non-existent class).
     * 3. Missing ID columns in database tables that look like foreign keys.
     *
     * @param  array<string, string[]>  $potentialRelations  Suspicious database columns discovered by scanner.
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
                } elseif (! is_subclass_of($related, \Illuminate\Database\Eloquent\Model::class)) {
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

    /**
     * Prepares relationship data for diagram generation.
     *
     * It maps model names to their plural snake_case table names and determines the
     * relationship links between them.
     *
     * @param  bool|null  $withExtraPivotTables  If true, pivot tables are included as separate nodes.
     * @return array<string, array<string|null, EloquentRelation>> A map of table connections.
     */
    public function getDiagramData(?bool $withExtraPivotTables = null): array
    {
        $withExtraPivotTables ??= $this->config->showIntermediateTables;
        $tables = [];
        foreach ($this->models as $modelName => $modelData) {
            foreach ($modelData->methods as $relationData) {
                $type = $relationData->type;

                $relatedModel = $relationData->related;

                $modelName1 = class_basename($modelName);
                $modelName2 = $relatedModel ? class_basename($relatedModel) : null;

                $tableName1 = Str::snake(Str::plural($modelName1));
                $tableName2 = $modelName2 ? Str::snake(Str::plural($modelName2)) : null;

                $names = [Str::snake($modelName1), Str::snake($modelName2 ?? '')];
                sort($names);
                $pivotTable = implode('_', $names);

                switch ($type) {
                    case EloquentRelation::hasOne:
                    case EloquentRelation::hasMany:
                    case EloquentRelation::morphOne:
                    case EloquentRelation::morphMany:
                    case EloquentRelation::morphToMany:
                    case EloquentRelation::morphedByMany:
                        if ($tableName2) {
                            $tables[$tableName1][$tableName2] = $type;
                        }
                        break;
                    case EloquentRelation::hasOneThrough:
                    case EloquentRelation::hasManyThrough:
                    case EloquentRelation::hasOneIndirect:
                        // no link
                        break;
                    case EloquentRelation::belongsToMany:
                        if ($tableName2) {
                            if ($withExtraPivotTables) {
                                $tables[$pivotTable][$tableName1] = $type;
                                $tables[$pivotTable][$tableName2] = $type;
                            } else {
                                $tables[$tableName1][$tableName2] = $type;
                            }
                        }
                        break;

                    case EloquentRelation::belongsTo:
                        if ($tableName2) {
                            $tables[$tableName1][$tableName2] = $type;
                        }
                        break;
                    case EloquentRelation::morphTo:
                    case EloquentRelation::isSingle:
                    case EloquentRelation::noRelation:
                        $tables[$tableName1][null] = $type;
                        break;
                }
            }
        }

        // bidirectional detection
        foreach ($tables as $t1 => $targets) {
            foreach ($targets as $t2 => $rel) {
                if ($t2 && isset($tables[$t2][$t1])) {
                    $tables[$t1][$t2] = EloquentRelation::bidirectional;
                    unset($tables[$t2][$t1]);
                }
            }
        }

        return array_filter($tables);
    }

    /**
     * Summarizes relationship information for each model.
     *
     * It distinguishes between direct (e.g., belongsTo) and indirect (e.g., hasManyThrough) relationships.
     *
     * @return array<string, array{direct: string[], indirect: string[]}>
     */
    public function getModelRelationsData(): array
    {
        $result = [];
        foreach ($this->models as $modelName => $modelData) {
            $modelShortName = class_basename($modelName);
            $result[$modelShortName] = [
                'direct' => [],
                'indirect' => [],
            ];
            foreach ($modelData->methods as $methodName => $relationData) {
                $type = $relationData->type;
                if ($type === EloquentRelation::noRelation || $type === EloquentRelation::isSingle) {
                    continue;
                }
                $related = $relationData->related ? class_basename($relationData->related) : 'n/a';
                $relationLabel = "$methodName ($related)";
                if ($type->isDirectRelation()) {
                    $result[$modelShortName]['direct'][] = $relationLabel;
                } else {
                    $result[$modelShortName]['indirect'][] = $relationLabel;
                }
            }
            sort($result[$modelShortName]['direct']);
            sort($result[$modelShortName]['indirect']);
        }
        ksort($result);

        return $result;
    }

    /**
     * Infers database tables and their expected foreign key columns from the relationship data.
     *
     * This is useful for generating documentation or verifying database schema.
     *
     * @return array<string, string[]> A mapping of table names to their identified foreign key columns.
     */
    public function getDatabaseTableData(): array
    {
        $tables = [];
        foreach ($this->models as $modelName => $modelData) {
            $modelShortName = class_basename($modelName);
            $tableName = Str::snake(Str::plural($modelShortName));
            if (! isset($tables[$tableName])) {
                $tables[$tableName] = [];
            }
            foreach ($modelData->methods as $methodName => $relationData) {
                $type = $relationData->type;
                switch ($type) {
                    case EloquentRelation::belongsTo:
                        $fk = $relationData->foreignKey ?: Str::snake($relationData->related ? class_basename($relationData->related) : '').'_id';
                        $tables[$tableName][] = $fk;
                        break;
                    case EloquentRelation::morphTo:
                        $tables[$tableName][] = $methodName.'_id';
                        $tables[$tableName][] = $methodName.'_type';
                        break;
                    case EloquentRelation::belongsToMany:
                        $pivot = $relationData->pivotTable;
                        if (! $pivot && $relationData->related) {
                            $names = [Str::snake($modelShortName), Str::snake(class_basename($relationData->related))];
                            sort($names);
                            $pivot = implode('_', $names);
                        }
                        if ($pivot) {
                            if (! isset($tables[$pivot])) {
                                $tables[$pivot] = [];
                            }
                            $tables[$pivot][] = Str::snake($modelShortName).'_id';
                            if ($relationData->related) {
                                $tables[$pivot][] = Str::snake(class_basename($relationData->related)).'_id';
                            }
                        }
                        break;
                    case EloquentRelation::morphToMany:
                    case EloquentRelation::morphedByMany:
                        $pivot = $relationData->pivotTable;
                        if ($pivot) {
                            if (! isset($tables[$pivot])) {
                                $tables[$pivot] = [];
                            }
                            // for morphToMany we expect something like 'taggable' name for the fields
                            // but we don't have it easily. Let's use the pivot table name singular if possible
                            $pivotSingular = Str::singular($pivot);
                            $tables[$pivot][] = $pivotSingular.'_id';
                            $tables[$pivot][] = $pivotSingular.'_type';
                            if ($relationData->related) {
                                $tables[$pivot][] = Str::snake(class_basename($relationData->related)).'_id';
                            }
                        }
                        break;
                }
            }
        }

        foreach ($tables as $name => $fields) {
            $tables[$name] = array_values(array_unique($fields));
            sort($tables[$name]);
        }
        ksort($tables);

        return $tables;
    }

    /**
     * Find reverse relations for a given relation.
     *
     * @return array<int, array{0: string, 1: string}>
     */
    public function getReverseRelations(string $modelName, string $methodName): array
    {
        $relation = $this->models[$modelName]->methods[$methodName] ?? null;
        if (! $relation || ! $relation->related) {
            return [];
        }

        $results = [];
        $targetModel = $relation->related;
        $expectedInverseType = $relation->type->inverse();

        if (isset($this->models[$targetModel])) {
            foreach ($this->models[$targetModel]->methods as $targetMethodName => $targetRelation) {
                if ($targetRelation->related === $modelName && $targetRelation->type === $expectedInverseType) {
                    $results[] = [$targetModel, $targetMethodName];
                }
            }
        }

        return $results;
    }

    /**
     * For a morphTo relation, find all models that morph to it.
     *
     * @return string[] List of model names
     */
    public function getMorphToTargets(string $modelName, string $methodName): array
    {
        $relation = $this->models[$modelName]->methods[$methodName] ?? null;
        if (! $relation || $relation->type !== EloquentRelation::morphTo) {
            return [];
        }

        $targets = [];
        foreach ($this->models as $otherModelName => $otherModelData) {
            foreach ($otherModelData->methods as $otherMethodName => $otherRelation) {
                if ($otherRelation->related === $modelName &&
                    ($otherRelation->type === EloquentRelation::morphOne || $otherRelation->type === EloquentRelation::morphMany)) {
                    $targets[] = $otherModelName;
                }
            }
        }

        return array_unique($targets);
    }
}
