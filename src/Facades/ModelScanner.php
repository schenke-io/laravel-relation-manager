<?php

namespace SchenkeIo\LaravelRelationManager\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the ModelScanner service, providing methods to scan models
 * for relationships and analyze database columns.
 *
 * @method static array<string, array<string, array{type: \SchenkeIo\LaravelRelationManager\Enums\Relation, related: string|null, pivotTable?: string, foreignKey?: string}>> scan(string $directory = null)
 * @method static array<string, array<int, string>> getDatabaseColumns(array<string, array<string, array{type: \SchenkeIo\LaravelRelationManager\Enums\Relation, related: string|null, pivotTable?: string, foreignKey?: string}>> $models)
 *
 * @see \SchenkeIo\LaravelRelationManager\Scanner\ModelScanner
 */
class ModelScanner extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \SchenkeIo\LaravelRelationManager\Scanner\ModelScanner::class;
    }
}
