<?php

namespace SchenkeIo\LaravelRelationManager\Scanner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;
use SchenkeIo\LaravelRelationManager\Exceptions\LaravelRelationManagerException;
use SchenkeIo\LaravelRelationManager\Support\FileHelper;
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
        if ($directory === null || $directory === 'app/Models') {
            $directory = PathResolver::getModelPath();
        }

        if (! File::isDirectory($directory)) {
            $absolutePath = PathResolver::getRealBasePath($directory);
            if (File::isDirectory($absolutePath)) {
                $directory = $absolutePath;
            } else {
                throw LaravelRelationManagerException::directoryNotFound($directory ?: 'null');
            }
        }

        /** @var array<string, array<string, array{type: EloquentRelation, related: string|null, pivotTable?: string, foreignKey?: string}>> $models */
        $models = [];
        foreach (File::allFiles($directory) as $file) {
            $className = FileHelper::getClassNameFromFile($file->getRealPath());
            if ($className && is_subclass_of($className, Model::class)) {
                /** @var class-string<Model> $className */
                $models[$className] = array_merge(
                    $models[$className] ?? [],
                    $this->scanModel($className, $models)
                );
            }
        }

        return $models;
    }

    /**
     * Analyzes a single Eloquent model class to discover its relationships.
     *
     * @param  class-string<Model>  $className  The name of the model class to scan.
     * @param  array<string, array<string, array{type: EloquentRelation, related: string|null, pivotTable?: string, foreignKey?: string}>>  $models  Reference to the overall models array to allow injecting reverse relations.
     * @return array<string, array{type: EloquentRelation, related: string|null, pivotTable?: string, foreignKey?: string}>
     *                                                                                                                      An array of relationship data for the given model.
     */
    protected function scanModel(string $className, array &$models): array
    {
        return (new ModelAnalyzer($className))->analyze($models);
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
        return (new DatabaseTableScanner)->getPotentialForeignKeys($models);
    }
}
