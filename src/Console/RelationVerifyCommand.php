<?php

namespace SchenkeIo\LaravelRelationManager\Console;

use Illuminate\Console\Command;
use SchenkeIo\LaravelRelationManager\Data\RelationshipData;
use SchenkeIo\LaravelRelationManager\Facades\ModelScanner;
use SchenkeIo\LaravelRelationManager\Support\PathResolver;

/**
 * Command to verify if the relationships defined in the JSON file
 * match the actual model implementation and to identify potential missing relations.
 */
class RelationVerifyCommand extends Command
{
    protected $signature = 'relation:verify';

    protected $description = 'Compares .relationships.json with the current model state';

    public function handle(): int
    {
        $this->info('Verifying relationships...');
        try {
            $models = ModelScanner::scan();
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
        $path = PathResolver::getRelationshipFilePath();

        $relationshipData = RelationshipData::loadFromFile($path);
        if (! $relationshipData) {
            $this->error("Failed to load or parse $path");

            return self::FAILURE;
        }

        // Output table of relationships
        $rows = [];
        foreach ($relationshipData->models as $modelName => $modelData) {
            foreach ($modelData->methods as $methodName => $methodData) {
                $rows[] = [
                    class_basename($modelName),
                    $methodName,
                    $methodData->type->name,
                    $methodData->related ? class_basename($methodData->related) : '-',
                ];
            }
        }
        $this->table(['Model', 'Method', 'Type', 'Related'], $rows);

        $errors = $relationshipData->validateImplementation($models);

        $potentialRelations = ModelScanner::getDatabaseColumns($models);
        $warnings = $relationshipData->getWarnings($potentialRelations);

        if (! empty($errors)) {
            $this->newLine();
            $this->error('Errors:');
            foreach ($errors as $error) {
                $this->line("  - $error");
            }
        }

        if (! empty($warnings)) {
            $this->newLine();
            $this->warn('Warnings:');
            foreach ($warnings as $warning) {
                $this->line("  - $warning");
            }
        }

        $this->newLine();
        if (empty($errors) && empty($warnings)) {
            $this->info('Relationships are in sync and no logic warnings found.');
        } elseif (empty($errors)) {
            $this->info('Relationships are in sync, but logic warnings were found.');
        } else {
            $this->error('Inconsistencies found between JSON and implementation.');
        }

        return empty($errors) ? self::SUCCESS : self::FAILURE;
    }
}
