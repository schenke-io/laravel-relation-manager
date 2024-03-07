<?php

namespace SchenkeIo\LaravelRelationManager\Define;

use SchenkeIo\LaravelRelationManager\Data\ClassData;

class ProjectContainer
{
    private static string $modelNameSpace = '';

    /**
     * @var array <string,mixed>
     */
    private static array $relations = [];

    private static array $errors = [];

    public static function clear(): void
    {
        self::$modelNameSpace = '';
        self::$relations = [];
        self::$errors = [];
    }

    public static function setModelNameSpace(string $modelNameSpace): void
    {
        self::$modelNameSpace = $modelNameSpace;
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
        if ($classFrom != '' & $classTo != '') {
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
        $class = ClassData::newFromName(self::$modelNameSpace, $modelClass);
        if (! $class->isClass) {
            self::addError(sprintf("%s - class '%s' not found in namespace '%s'",
                $errorInfo, $modelClass, self::$modelNameSpace
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
        $tables = [];
        foreach (self::$relations as $primModel => $modelSet) {
            /**
             * @var string $secModel
             * @var RelationsEnum $relation
             */
            foreach ($modelSet as $secModel => $relation) {
                $relation->setTableLinks($primModel, $secModel, $tables);
                //                dump("$primModel => $secModel ({$relation->name})\n");
                //                $return .= $relation->getMermaidLine($primModel, $secModel);
            }
        }
        foreach ($tables as $table1 => $data) {
            foreach ($data as $table2 => $isSolid) {
                $arrow = $isSolid ? '====>' : '---->';
                $return .= "$table1 $arrow $table2\n";
            }
        }

        return $return;
    }
}
