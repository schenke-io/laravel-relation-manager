<?php

namespace SchenkeIo\LaravelRelationManager\Scanner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Service to scan database tables for potential foreign key columns
 * that are not yet defined as relationships in Eloquent models.
 */
class DatabaseTableScanner
{
    /**
     * Identifies database columns that look like foreign keys (ending in '_id') but
     * are not yet associated with any discovered Eloquent relationship.
     *
     * @param  array<string, array<string, mixed>>  $models  The list of models and their discovered relationships.
     * @return array<string, array<int, string>> A mapping of model classes to lists of suspicious foreign key columns.
     */
    public function getPotentialForeignKeys(array $models): array
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
                        if (! $this->isColumnHandled($column, $relations)) {
                            $potentialRelations[$className][] = $column;
                        }
                    }
                }
            } catch (\Throwable $e) {
                // table might not exist or other database errors
            }
        }

        return $potentialRelations;
    }

    /**
     * Checks if a database column is already handled by one of the model's relationships.
     *
     * @param  array<string, mixed>  $relations
     */
    protected function isColumnHandled(string $column, array $relations): bool
    {
        foreach ($relations as $relData) {
            if (isset($relData['foreignKey']) && $relData['foreignKey'] === $column) {
                return true;
            }
        }

        return false;
    }
}
