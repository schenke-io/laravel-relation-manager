<?php

namespace SchenkeIo\LaravelRelationManager\Define;

use Illuminate\Support\Str;
use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Enums\DiagramDirection;
use SchenkeIo\LaravelRelationManager\Enums\Relations;
use SchenkeIo\LaravelRelationManager\Writer\GetDiagramm;
use SchenkeIo\LaravelRelationManager\Writer\GetTable;

class ProjectContainer
{
    public const CONFIG_KEY_MODEL_NAME_SPACE = 'relation-manager.modelNameSpace';

    public const CONFIG_KEY_PROJECT_TEST_CLASS = 'relation-manager.projectTestClass';

    public const CONFIG_KEY_EXTENDED_TEST_CLASS = 'relation-manager.extendedTestClass';

    public const CONFIG_KEY_MARKDOWN_FILE = 'relation-manager.markdownFile';

    public const CONFIG_KEY_TEST_COMMAND = 'relation-manager.testCommand';

    public const CONFIG_KEY_USE_MERMAID_DIAGRAMM = 'relation-manager.useMermaidDiagram';

    public const CONFIG_KEY_TEST_DATABASE = 'relation-manager.testDatabase';

    public static DiagramDirection $diagrammDirection = DiagramDirection::LR;

    /**
     * @var array <string,array <string,Relations>>
     */
    private static array $relations = [];

    private static array $errors = [];

    private static array $unknownModels = [];

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

    public static function addRelation(string $modelFrom, string $modelTo, Relations $relation): void
    {
        $classFrom = self::getModelClass($modelFrom);
        $classTo = self::getModelClass($modelTo);
        if ($relation == Relations::morphTo) {
            /*
             * morphTo is one method covering all morphs
             * we collect these separately and have this method be related to no model
             */
            self::$morphToRelations[$classFrom][] = class_basename($classTo);
            $classTo = '';
        }
        if ($classFrom != '') {
            if (isset(self::$relations[$classFrom][$classTo])) {
                if (self::$relations[$classFrom][$classTo] != $relation) {
                    self::addError(sprintf(
                        "unable to overwrite relation '%s' from '%s' to '%s' with '%s'",
                        self::$relations[$classFrom][$classTo]->name,
                        $modelFrom,
                        $modelTo,
                        $relation->name
                    )
                    );
                }
            } else {
                self::$relations[$classFrom][$classTo] = $relation;
            }
        }
    }

    public static function getRelations(): array
    {
        ksort(self::$relations);

        return self::$relations;
    }

    public static function getUnknownModels(): array
    {
        return self::$unknownModels;
    }

    public static function getModelClass(string $modelClass): string
    {
        $class = ClassData::newFromName(config(self::CONFIG_KEY_MODEL_NAME_SPACE), $modelClass);
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

    public static function getErrors(): array
    {
        return self::$errors;
    }

    public static function getTableFields(): array
    {
        $tables = [];
        /*
         * load all tables of each model
         */
        foreach (self::$relations as $primModel => $data) {
            $primClass = ClassData::take($primModel);
            $tableName = Str::snake(Str::plural(class_basename($primModel)));
            $tables[$tableName] = [];
        }
        foreach (self::getDatabaseData() as $table1 => $table1Data) {
            ksort($table1Data);
            /**
             * @var Relations $relation
             */
            foreach ($table1Data as $table2 => $relation) {
                if (strlen($table2) > 1) {
                    if ($relation->isMorph()) {
                        $tables[$table1][] = Str::singular($table1).'able_id';
                        $tables[$table1][] = Str::singular($table1).'able_type';
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

    public static function getDatabaseTable(): array
    {
        $return = [];
        foreach (self::getTableFields() as $table => $fields) {
            $return[] = [$table, implode(', ', $fields)];
        }

        return [['table', 'required fields'], $return];
    }

    public static function getRelationTable(): array
    {
        $return = [];
        foreach (self::$relations as $primModel => $modelSet) {
            ksort($modelSet);
            $primClass = ClassData::take($primModel);
            $directRelation = self::$morphToRelations[$primModel] ?? [];
            sort($directRelation);
            $indirectRelation = [];
            /**
             * @var Relations $relation
             */
            foreach ($modelSet as $secModel => $relation) {
                if ($relation->isDirectRelation()) {
                    $directRelation[] = class_basename($secModel);
                } else {
                    $indirectRelation[] = class_basename($secModel);
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

    public static function getDiagrammCode(): string
    {
        if (config(self::CONFIG_KEY_USE_MERMAID_DIAGRAMM)) {
            return GetDiagramm::getMermaidCode(
                self::getDatabaseData(),
                self::$diagrammDirection
            );
        } else {
            GetDiagramm::writeGraphvizFile(self::getDatabaseData(),
                self::$diagrammDirection,
                config(self::CONFIG_KEY_MARKDOWN_FILE));

            return GetDiagramm::getGraphvizCode();
        }

    }

    public static function getDatabaseData(): array
    {
        $table = [];
        foreach (self::$relations as $primModel => $modelSet) {
            /**
             * @var string $secModel
             * @var Relations $relation
             */
            foreach ($modelSet as $secModel => $relation) {
                $relation->setTableLinks($primModel, $secModel, $table);
            }
        }

        return $table;
    }
}
