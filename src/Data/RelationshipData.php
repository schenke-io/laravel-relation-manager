<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SchenkeIo\LaravelRelationManager\Enums\Relation;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class RelationshipData extends Data
{
    public function __construct(
        public readonly ConfigData $config = new ConfigData,
        /** @var array<string, ModelData> */
        public readonly array $models = [],
    ) {}

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

    public function saveToFile(string $path): bool
    {
        $json = $this->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return (bool) File::put($path, $json);
    }

    /**
     * @param  array<string, array<string, array<string, mixed>>>  $currentModels
     * @return array<string> List of error messages
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
                        $errors[] = "Relation $model::$method found in implementation but missing in JSON";
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
                    $errors[] = "Relation $model::$method missing in implementation";

                    continue;
                }

                $currentData = $currentModels[$model][$method];

                // Compare type
                $expectedType = $expectedData->type->name;
                $currentType = ($currentData['type'] instanceof Relation) ? $currentData['type']->name : $currentData['type'];

                if ($currentType !== $expectedType) {
                    $errors[] = "Relation $model::$method type mismatch: expected $expectedType, got $currentType";
                }

                // Compare related model
                $expectedRelated = $expectedData->related;
                $currentRelated = $currentData['related'] ?? null;

                if ($expectedRelated && $currentRelated !== $expectedRelated) {
                    $errors[] = "Relation $model::$method related model mismatch: expected $expectedRelated, got $currentRelated";
                }

                // Compare foreign key
                $expectedForeignKey = $expectedData->foreignKey;
                $currentForeignKey = $currentData['foreignKey'] ?? null;
                if ($expectedForeignKey && $currentForeignKey !== $expectedForeignKey) {
                    $errors[] = "Relation $model::$method foreign key mismatch: expected $expectedForeignKey, got $currentForeignKey";
                }

                // Compare pivot table
                $expectedPivotTable = $expectedData->pivotTable;
                $currentPivotTable = $currentData['pivotTable'] ?? null;
                if ($expectedPivotTable && $currentPivotTable !== $expectedPivotTable) {
                    $errors[] = "Relation $model::$method pivot table mismatch: expected $expectedPivotTable, got $currentPivotTable";
                }
            }
        }

        return $errors;
    }

    /**
     * @param  array<string, string[]>  $potentialRelations
     * @return string[]
     */
    public function getWarnings(array $potentialRelations = []): array
    {
        $warnings = [];

        foreach ($this->models as $model => $modelData) {
            foreach ($modelData->methods as $method => $data) {
                $typeEnum = $data->type;

                // Asymmetry check
                if ($typeEnum === Relation::belongsToMany) {
                    $relatedModel = $data->related;
                    if (! $relatedModel) {
                        continue;
                    }

                    $foundInverse = false;
                    if (isset($this->models[$relatedModel])) {
                        foreach ($this->models[$relatedModel]->methods as $inverseData) {
                            $inverseType = $inverseData->type;
                            if ($inverseType === Relation::belongsToMany && $inverseData->related === $model) {
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
                    if ($typeEnum !== Relation::morphTo && $typeEnum !== Relation::noRelation) {
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
     * @return array<string, array<string|null, Relation>>
     */
    public function getDiagramData(bool $withExtraPivotTables = false): array
    {
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
                    case Relation::hasOne:
                    case Relation::hasMany:
                    case Relation::morphOne:
                    case Relation::morphMany:
                        if ($tableName2) {
                            $tables[$tableName2][$tableName1] = $type;
                        }
                        break;
                    case Relation::hasOneThrough:
                    case Relation::hasManyThrough:
                    case Relation::hasOneIndirect:
                        // no link
                        break;
                    case Relation::belongsToMany:
                        if ($tableName2) {
                            if ($withExtraPivotTables) {
                                $tables[$pivotTable][$tableName1] = $type;
                                $tables[$pivotTable][$tableName2] = $type;
                            } else {
                                if ($tableName1 > $tableName2) {
                                    $tables[$tableName1][$tableName2] = $type;
                                }
                            }
                        }
                        break;
                    case Relation::morphToMany:
                    case Relation::morphedByMany:
                        if ($tableName2) {
                            $tables[$tableName2][$tableName1] = $type;
                        }
                        break;

                    case Relation::belongsTo:
                        if ($tableName2) {
                            $tables[$tableName1][$tableName2] = $type;
                        }
                        break;
                    case Relation::morphTo:
                    case Relation::isSingle:
                    case Relation::noRelation:
                        $tables[$tableName1][null] = $type;
                        break;
                }
            }
        }

        return $tables;
    }

    /**
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
                if ($type === Relation::noRelation || $type === Relation::isSingle) {
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
     * @return array<string, string[]>
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
                    case Relation::belongsTo:
                        $fk = $relationData->foreignKey ?: Str::snake($relationData->related ? class_basename($relationData->related) : '').'_id';
                        $tables[$tableName][] = $fk;
                        break;
                    case Relation::morphTo:
                        $tables[$tableName][] = $methodName.'_id';
                        $tables[$tableName][] = $methodName.'_type';
                        break;
                    case Relation::belongsToMany:
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
                    case Relation::morphToMany:
                    case Relation::morphedByMany:
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
}
