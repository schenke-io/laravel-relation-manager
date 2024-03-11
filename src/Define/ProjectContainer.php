<?php

namespace SchenkeIo\LaravelRelationManager\Define;

use Illuminate\Support\Str;
use SchenkeIo\LaravelRelationManager\Data\ClassData;

class ProjectContainer
{
    public const CONFIG_KEY_MODEL_NAME_SPACE = 'relation-manager.modelNameSpace';

    public const CONFIG_KEY_PROJECT_TEST_CLASS = 'relation-manager.projectTestClass';

    public const CONFIG_KEY_EXTENDED_TEST_CLASS = 'relation-manager.extendedTestClass';

    public const CONFIG_KEY_MARKDOWN_FILE = 'relation-manager.markdownFile';

    public const CONFIG_KEY_TEST_COMMAND = 'relation-manager.testCommand';

    /**
     * @var array <string,mixed>
     */
    private static array $relations = [];

    private static array $errors = [];

    public static function clear(): void
    {
        self::$relations = [];
        self::$errors = [];
    }

    public static function addModel(string $modelName): void
    {
        $className = self::getModelClass($modelName, 'first model');
        if ($className != '') {
            self::$relations[$className] = self::$relations[$className] ?? [];
        }
    }

    public static function addRelation(string $modelFrom, string $modelTo, RelationsEnum $relation): void
    {
        $classFrom = self::getModelClass($modelFrom, 'first model');
        $classTo = self::getModelClass($modelTo, 'second model');
        if ($relation == RelationsEnum::morphTo) {
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

    public static function getModelClass(string $modelClass, string $errorInfo): string
    {
        $class = ClassData::newFromName(config(self::CONFIG_KEY_MODEL_NAME_SPACE), $modelClass);
        if (! $class->isClass) {
            self::addError(sprintf("%s - class '%s' not found in namespace '%s'",
                $errorInfo, $modelClass, config(self::CONFIG_KEY_MODEL_NAME_SPACE)
            ));

            return '';
        }
        if (! $class->isModel) {
            self::addError(sprintf('%s is not a model', $modelClass));

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

    public static function getDatabaseTable(): array
    {
        $tables = [];
        /*
         * load all tables of each model
         */
        foreach (self::$relations as $primModel => $data) {
            $tables[Str::snake(Str::plural(class_basename($primModel)))] = [];
        }
        foreach (self::getDatabaseData() as $table1 => $table1Data) {
            ksort($table1Data);
            /**
             * @var RelationsEnum $relation
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
            $return[] = [$table, implode(', ', $keys)];
        }

        return $return;
    }

    public static function getRelationTable(): array
    {
        $return = [];
        foreach (self::$relations as $primModel => $modelSet) {
            ksort($modelSet);
            $return[] = [
                class_basename($primModel),
                implode(', ', array_map(fn ($x) => class_basename($x), array_keys($modelSet))),
            ];
        }
        ksort($return);

        return $return;
    }

    public static function getMarkdownRelationTable(): string
    {
        $rows = '';
        foreach (self::getRelationTable() as [$primModel, $relatedModels]) {
            $rows .= "<tr><td>$primModel</td><td>$relatedModels</td></tr>\n";
        }

        return <<<HTML
<table>
<tr><th>model</th><th>... has relations to</th></tr>
$rows
</table>
HTML;

    }

    public static function getMermaidCode(): string
    {
        $return = '';
        $tables = self::getDatabaseData();
        foreach ($tables as $table1 => $data) {
            foreach ($data as $table2 => $isSolid) {
                $arrow = $isSolid ? '====>' : '---->';
                $return .= "$table1 $arrow $table2\n";
            }
        }

        return $return;
    }

    public static function getDatabaseData(): array
    {
        $table = [];
        foreach (self::$relations as $primModel => $modelSet) {
            /**
             * @var string $secModel
             * @var RelationsEnum $relation
             */
            foreach ($modelSet as $secModel => $relation) {
                $relation->setTableLinks($primModel, $secModel, $table);
            }
        }

        return $table;
    }
}
