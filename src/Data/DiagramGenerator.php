<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use Illuminate\Support\Str;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;

/**
 * Service to generate diagram data and summaries from relationship data.
 *
 * This class processes the relationship metadata collected from models to
 * produce structured data suitable for various output formats. It can
 * generate connection maps for Mermaid or Graphviz diagrams, summarize
 * direct and indirect relationships per model, and infer database table
 * structures including expected foreign keys and pivot tables. It also
 * provides utility methods to find reverse and polymorphic relationships.
 */
class DiagramGenerator
{
    /**
     * @param  array<string, ModelData>  $models
     */
    public function __construct(
        protected array $models,
        protected ConfigData $config
    ) {}

    /**
     * Prepares relationship data for diagram generation.
     *
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

        return array_values(array_unique($targets));
    }
}
