<?php

namespace SchenkeIo\LaravelRelationManager\Define;

use Illuminate\Support\Str;
use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Enums\ConfigKey;
use SchenkeIo\LaravelRelationManager\Enums\DiagramDirection;
use SchenkeIo\LaravelRelationManager\Enums\Relation;
use SchenkeIo\LaravelRelationManager\Writer\GetDiagramm;
use SchenkeIo\LaravelRelationManager\Writer\GetTable;

class ProjectContainer
{
    public static DiagramDirection $diagrammDirection = DiagramDirection::LR;

    /**
     * @var array<string, array<string, list<Relation>>>
     */
    private static array $relations = [];

    /** @var list<string> */
    private static array $errors = [];

    /** @var list<string> */
    private static array $unknownModels = [];

    /** @var array<string, list<string>> */
    private static array $morphToRelations = [];

    public static function clear(): void
    {
        self::$relations = [];
        self::$errors = [];
        self::$unknownModels = [];
        self::$morphToRelations = [];
    }

    public static function addModel(string $modelName): void
    {
        $className = self::getModelClass($modelName);
        if ($className != '') {
            self::$relations[$className] = self::$relations[$className] ?? [];
        } else {
            self::$unknownModels[] = $modelName;
        }
    }

    public static function addRelation(string $modelFrom, string $modelTo, Relation $relation): void
    {
        $classFrom = self::getModelClass($modelFrom);
        if (! $classFrom) {
            return;
        }

        $classTo = self::getModelClass($modelTo);
        if ($relation == Relation::morphTo) {
            /*
             * morphTo is one method covering all morphs
             * we collect these separately and have this method be related to no model
             */
            self::$morphToRelations[$classFrom][] = class_basename($classTo);
            $classTo = '';
            if (in_array($relation, self::$relations[$classFrom][$classTo] ?? [])) {
                // only add it once
                return;
            }
        }
        if ($relation == Relation::morphToMany) {

        }
        self::$relations[$classFrom][$classTo][] = $relation;
    }

    /**
     * @return array<string, array<string, list<Relation>>>
     */
    public static function getRelations(): array
    {
        ksort(self::$relations);

        return self::$relations;
    }

    /**
     * @return list<string>
     */
    public static function getUnknownModels(): array
    {
        return self::$unknownModels;
    }

    public static function getModelClass(string $modelClass): string
    {
        $class = ClassData::newFromName((string) ConfigKey::MODEL_NAME_SPACE->get(), $modelClass);
        if (! $class->isClass) {
            return '';
        }
        if (! $class->isModel) {
            return '';
        }

        return $class->className;
    }

    public static function addError(string $msg): void
    {
        self::$errors[] = $msg;
    }

    /**
     * @return list<string>
     */
    public static function getErrors(): array
    {
        return self::$errors;
    }

    /**
     * @return array<string, list<string>>
     */
    public static function getTableFields(bool $withExtraPivotTables): array
    {
        $tables = [];
        /*
         * load all tables of each model
         */
        foreach (self::$relations as $primModel => $data) {
            $tableName = Str::snake(Str::plural(class_basename($primModel)));
            $tables[$tableName] = [];
        }
        foreach (self::getDatabaseData($withExtraPivotTables) as $table1 => $table1Data) {
            ksort($table1Data);
            foreach ($table1Data as $table2 => $relation) {
                if (strlen($table2) > 1) {
                    if ($relation->isMorph()) {
                        $base = Str::singular($table1);
                        $base = str_ends_with($base, 'able') ? $base : $base.'able';
                        $tables[$table1][] = $base.'_id';
                        $tables[$table1][] = $base.'_type';
                    } else {
                        $tables[$table1][] = Str::singular($table2).'_id';
                    }
                }
            }
        }
        ksort($tables);
        $return = [];
        foreach ($tables as $table => $keys) {
            $keys = array_unique($keys);
            sort($keys);
            $return[$table] = $keys;
        }

        return $return;
    }

    /**
     * @return array{0: list<string>, 1: list<list<string>>}
     */
    public static function getDatabaseTable(): array
    {
        $return = [];
        foreach (self::getTableFields(true) as $table => $fields) {
            $return[] = [$table, implode(', ', $fields)];
        }

        return [['table', 'required fields'], $return];
    }

    /**
     * @return array{0: list<string>, 1: list<list<string>>}
     */
    public static function getRelationTable(): array
    {
        $return = [];
        foreach (self::$relations as $primModel => $modelSet) {
            ksort($modelSet);
            $primClass = ClassData::take($primModel);
            $directRelation = self::$morphToRelations[$primModel] ?? [];
            sort($directRelation);
            $indirectRelation = [];
            foreach ($modelSet as $secModel => $relations) {
                foreach ($relations as $relation) {
                    if ($relation->isDirectRelation()) {
                        $directRelation[] = class_basename($secModel);
                    } else {
                        $indirectRelation[] = class_basename($secModel);
                    }
                }
            }

            $return[] = [
                class_basename($primClass->className),
                implode(', ', $directRelation),
                implode(', ', $indirectRelation),
            ];
        }
        foreach (self::$unknownModels as $model) {
            $return[] = ["$model (not defined)", '', ''];
        }

        return [['model', 'direct', 'indirect'], $return];
    }

    public static function getMarkdownDatabaseTable(): string
    {
        return GetTable::getHtml(self::getDatabaseTable());
    }

    public static function getMarkdownRelationTable(): string
    {
        return GetTable::getHtml(self::getRelationTable());
    }

    public static function getDiagrammCode(bool $withExtraPivotTables): string
    {
        if (ConfigKey::USE_MERMAID_DIAGRAMM->get()) {
            return GetDiagramm::getMermaidCode(
                self::getDatabaseData($withExtraPivotTables),
                self::$diagrammDirection
            );
        } else {
            GetDiagramm::writeGraphvizFile(self::getDatabaseData($withExtraPivotTables),
                self::$diagrammDirection,
                (string) ConfigKey::MARKDOWN_FILE->get());

            return GetDiagramm::getGraphvizCode();
        }

    }

    /**
     * @return array<string, array<string, Relation>>
     */
    public static function getDatabaseData(bool $withExtraPivotTables): array
    {
        $table = [];
        foreach (self::$relations as $primModel => $baseModelRelations) {
            foreach ($baseModelRelations as $secModel => $relations) {
                foreach ($relations as $relation) {
                    $relation->setTableLinks(
                        $primModel,
                        $secModel,
                        $table,
                        $withExtraPivotTables
                    );
                }
            }
        }

        return $table;
    }
}
