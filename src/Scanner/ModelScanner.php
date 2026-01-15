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
use SchenkeIo\LaravelRelationManager\Enums\Relation;
use SchenkeIo\LaravelRelationManager\Exceptions\LaravelRelationManagerException;
use SchenkeIo\LaravelRelationManager\Support\PathResolver;

/**
 * Service to scan Eloquent models for relationships, analyzing method
 * return types, attributes, and database columns.
 */
class ModelScanner
{
    /**
     * @return array<string, array<string, array{type: Relation, related: string|null, pivotTable?: string, foreignKey?: string}>>
     *
     * @throws LaravelRelationManagerException
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
     * @param  class-string  $className
     * @param  array<string, array<string, array{type: Relation, related: string|null, pivotTable?: string, foreignKey?: string}>>  $models
     * @return array<string, array{type: Relation, related: string|null, pivotTable?: string, foreignKey?: string}>
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
                if ($attrInstance->type !== Relation::noRelation) {
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
                        if ($inverseType !== Relation::noRelation) {
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
                    $relationEnum = Relation::fromRelationName($shortTypeName);

                    if ($relationEnum !== Relation::noRelation) {
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
                    if ($relations[$methodName]['type'] === Relation::belongsToMany && method_exists($relationObject, 'getTable')) {
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
     * @param  array<string, array<string, array{type: Relation, related: string|null, pivotTable?: string, foreignKey?: string}>>  $models
     * @return array<string, array<int, string>>
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
