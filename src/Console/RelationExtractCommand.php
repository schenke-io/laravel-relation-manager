<?php

namespace SchenkeIo\LaravelRelationManager\Console;

use Illuminate\Console\Command;
use SchenkeIo\LaravelRelationManager\Data\ConfigData;
use SchenkeIo\LaravelRelationManager\Data\ModelData;
use SchenkeIo\LaravelRelationManager\Data\RelationData;
use SchenkeIo\LaravelRelationManager\Data\RelationshipData;
use SchenkeIo\LaravelRelationManager\Facades\ModelScanner;
use SchenkeIo\LaravelRelationManager\Support\PathResolver;

/**
 * Command to scan the project models and export their relationships
 * to a JSON file.
 */
class RelationExtractCommand extends Command
{
    protected $signature = 'relation:extract';

    protected $description = 'Scans models and writes to .relationships.json';

    public function handle(): int
    {
        $path = PathResolver::getRelationshipFilePath();
        $relationshipData = RelationshipData::loadFromFile($path);
        $directory = $relationshipData?->config?->modelPath;

        $this->info('Scanning models...');
        try {
            $scannedModels = ModelScanner::scan($directory);
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $models = [];
        foreach ($scannedModels as $modelClass => $methods) {
            $methodData = [];
            foreach ($methods as $methodName => $data) {
                $methodData[$methodName] = RelationData::from($data);
            }
            $models[$modelClass] = new ModelData(methods: $methodData);
        }

        $relationshipData = new RelationshipData(
            config: $relationshipData->config ?? new ConfigData,
            models: $models
        );

        if ($relationshipData->saveToFile($path)) {
            $this->info("Relationships exported to $path");

            return self::SUCCESS;
        }

        $this->error("Failed to export relationships to $path");

        return self::FAILURE;
    }
}
