<?php

namespace SchenkeIo\LaravelRelationManager\Scanner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use SchenkeIo\LaravelRelationManager\Attributes\Relation as RelationAttribute;
use SchenkeIo\LaravelRelationManager\Data\RelationshipData;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;
use SchenkeIo\LaravelRelationManager\Exceptions\LaravelRelationManagerException;
use SchenkeIo\LaravelRelationManager\Support\PathResolver;

/**
 * Service to scan Eloquent models for relationships, analyzing method
 * return types, attributes, and database columns.
 *
 * This scanner traverses a directory of model files, identifies those that
 * extend Eloquent's Model class, and inspects their public methods to find
 * relationship definitions. It uses reflection to check return types and
 * custom #[Relation] attributes. Additionally, it can probe database
 * schema for potential missing relationship definitions based on foreign key naming conventions.
 */
class ModelScanner
{
    /**
     * Scans the specified directory (or default model paths) for Eloquent models
     * and extracts their relationship metadata.
     *
     * @param  string|null  $directory  The directory to scan. If null, it tries to resolve from config or defaults to app/Models.
     * @return array<string, array<string, array{type: EloquentRelation, related: string|null, pivotTable?: string, foreignKey?: string}>>
     *                                                                                                                                     An associative array where keys are model class names and values are arrays of their relationships.
     *
     * @throws LaravelRelationManagerException If the directory is not found.
     */
    public function scan(?string $directory = null): array
    {
        if ($directory === null) {
            $path = PathResolver::getRelationshipFilePath();
            $relationshipData = RelationshipData::loadFromFile($path);
            if ($relationshipData && $relationshipData->config->modelPath) {
                $jsonPath = $relationshipData->config->modelPath;
                if (File::isDirectory($jsonPath)) {
                    $directory = $jsonPath;
                } elseif (File::isDirectory(base_path($jsonPath))) {
                    $directory = base_path($jsonPath);
                }
            }
        }

        if ($directory === null) {
            $authModel = config('auth.providers.users.model');
            if ($authModel && class_exists($authModel)) {
                $reflection = new ReflectionClass($authModel);
                $path = $reflection->getFileName();
                if ($path) {
                    $directory = dirname($path);
                }
            }
        }

        $directory = $directory ?: app_path('Models');

        if (! File::isDirectory($directory)) {
            throw LaravelRelationManagerException::directoryNotFound($directory ?: 'null');
        }

        $models = [];
        foreach (File::allFiles($directory) as $file) {
            $className = $this->getClassNameFromFile($file->getRealPath());
            if ($className && is_subclass_of($className, Model::class)) {
                $models[$className] = array_merge(
                    $models[$className] ?? [],
                    $this->scanModel($className, $models)
                );
            }
        }

        return $models;
    }

    /**
     * Extracts the full qualified class name from a PHP file by parsing its namespace and class name.
     *
     * @param  string  $path  The full path to the PHP file.
     * @return string|null The fully qualified class name or null if not found.
     */
    protected function getClassNameFromFile(string $path): ?string
    {
        $content = File::get($path);
        if (preg_match('/namespace\s+(.+?);/', $content, $matches)) {
            $namespace = $matches[1];
            if (preg_match('/class\s+(\w+)/', $content, $matches)) {
                return $namespace.'\\'.$matches[1];
            }
        }

        return null;
    }

    /**
     * Analyzes a single Eloquent model class to discover its relationships.
     *
     * It uses reflection to inspect public methods without parameters. It looks for:
     * 1. The #[Relation] attribute, which takes precedence.
     * 2. Method return types matching known Eloquent relationship classes.
     *
     * For discovered relationships, it attempts to instantiate the model and call the method
     * to obtain additional details like the related model class, pivot table name, or foreign key.
     *
     * @param  class-string  $className  The name of the model class to scan.
     * @param  array<string, array<string, array{type: EloquentRelation, related: string|null, pivotTable?: string, foreignKey?: string}>>  $models  Reference to the overall models array to allow injecting reverse relations.
     * @return array<string, array{type: EloquentRelation, related: string|null, pivotTable?: string, foreignKey?: string}>
     *                                                                                                                      An array of relationship data for the given model.
     */
    protected function scanModel(string $className, array &$models): array
    {
        /** @var class-string<Model> $className */
        $reflection = new ReflectionClass($className);
        $relations = [];
        $modelInstance = null;

        // Scan methods for return types
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $methodName = $method->getName();
            if ($method->getDeclaringClass()->getName() !== $className) {
                continue;
            }
            if ($method->getNumberOfParameters() > 0) {
                continue;
            }

            // Check for #[Relation] attribute (overrides everything)
            $attributes = $method->getAttributes(RelationAttribute::class);
            if (! empty($attributes)) {
                /** @var RelationAttribute $attrInstance */
                $attrInstance = $attributes[0]->newInstance();
                if ($attrInstance->type !== EloquentRelation::noRelation) {
                    $relations[$methodName] = [
                        'type' => $attrInstance->type,
                        'related' => $attrInstance->related,
                    ];

                    /*
                     * Handle addReverse parameter:
                     * If true, it automatically injects the inverse relation into the related model
                     */
                    if ($attrInstance->addReverse && $attrInstance->related) {
                        $inverseType = $attrInstance->type->inverse();
                        if ($inverseType !== EloquentRelation::noRelation) {
                            $inverseMethod = strtolower(class_basename($className));
                            if (! isset($models[$attrInstance->related])) {
                                $models[$attrInstance->related] = [];
                            }
                            $models[$attrInstance->related][$inverseMethod] = [
                                'type' => $inverseType,
                                'related' => $className,
                            ];
                        }
                    }
                } else {
                    /*
                     * explicitly marked as noRelation via attribute,
                     * so we exclude this method from relationship discovery
                     */
                    $relations[$methodName] = false;
                }
            }

            // Check return type if not already found via attribute
            if (! isset($relations[$methodName])) {
                $returnType = $method->getReturnType();
                if ($returnType instanceof ReflectionNamedType) {
                    $typeName = $returnType->getName();
                    $shortTypeName = class_basename($typeName);
                    $relationEnum = EloquentRelation::fromRelationName($shortTypeName);

                    if ($relationEnum !== EloquentRelation::noRelation) {
                        $relations[$methodName] = [
                            'type' => $relationEnum,
                            'related' => null,
                        ];
                    }
                }
            }

            if (isset($relations[$methodName]) && $relations[$methodName] !== false) {
                // Try to get more info by calling the method
                try {
                    $modelInstance = $modelInstance ?: new $className;
                    /** @var object $relationObject */
                    $relationObject = $modelInstance->$methodName();
                    if (method_exists($relationObject, 'getRelated')) {
                        $related = $relationObject->getRelated();
                        if ($related instanceof Model) {
                            $relations[$methodName]['related'] = get_class($related);
                        }
                    }
                    if ($relations[$methodName]['type'] === EloquentRelation::belongsToMany && method_exists($relationObject, 'getTable')) {
                        $relations[$methodName]['pivotTable'] = (string) $relationObject->getTable();
                    }
                    if (method_exists($relationObject, 'getForeignKeyName')) {
                        $relations[$methodName]['foreignKey'] = (string) $relationObject->getForeignKeyName();
                    }
                } catch (\Throwable $e) {
                }
            }
        }

        return array_filter($relations);
    }

    /**
     * Identifies database columns that look like foreign keys (ending in '_id') but
     * are not yet associated with any discovered Eloquent relationship.
     *
     * This helps in identifying missing relationship definitions in the model classes.
     *
     * @param  array<string, array<string, array{type: EloquentRelation, related: string|null, pivotTable?: string, foreignKey?: string}>>  $models  The list of models and their discovered relationships.
     * @return array<string, array<int, string>> A mapping of model classes to lists of suspicious foreign key columns.
     */
    public function getDatabaseColumns(array $models): array
    {
        $potentialRelations = [];
        foreach ($models as $className => $relations) {
            try {
                /** @var Model $modelInstance */
                $modelInstance = new $className;
                $table = $modelInstance->getTable();
                $columns = Schema::getColumnListing($table);

                foreach ($columns as $column) {
                    if (str_ends_with($column, '_id')) {
                        // check if this column is already handled by a relation
                        $found = false;
                        foreach ($relations as $relName => $relData) {
                            if (isset($relData['foreignKey']) && $relData['foreignKey'] === $column) {
                                $found = true;
                                break;
                            }
                        }
                        if (! $found) {
                            $potentialRelations[$className][] = $column;
                        }
                    }
                }
            } catch (\Throwable $e) {
                // table might not exist
            }
        }

        return $potentialRelations;
    }
}
