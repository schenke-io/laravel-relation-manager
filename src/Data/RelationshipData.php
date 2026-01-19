<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * Data Transfer Object for all relationship data of the project.
 *
 * This class serves as the central repository for relationship metadata
 * extracted from Eloquent models. It includes configuration settings and
 * a collection of model-specific relationship data. It provides methods
 * for loading from and saving to JSON files, validating the current
 * implementation against the stored data, generating warnings for
 * inconsistencies, and preparing data for visualization (diagrams).
 */
#[MapName(SnakeCaseMapper::class)]
class RelationshipData extends Data
{
    public function __construct(
        public readonly ConfigData $config = new ConfigData,
        /** @var array<string, ModelData> */
        public readonly array $models = [],
    ) {}

    /**
     * Custom instantiation logic to handle older data formats or nested arrays.
     *
     * @param  mixed  ...$payloads  Payloads to create the instance from.
     */
    public static function from(mixed ...$payloads): static
    {
        $data = $payloads[0] ?? [];
        if (is_array($data)) {
            if (isset($data['models']) && is_array($data['models'])) {
                foreach ($data['models'] as $modelName => $modelData) {
                    if (is_array($modelData) && ! isset($modelData['methods'])) {
                        $data['models'][$modelName] = [
                            'methods' => $modelData,
                        ];
                    }
                }
            }
            if (! isset($data['config'])) {
                $data['config'] = [];
            }
        }

        return parent::from($data);
    }

    /**
     * Loads relationship data from a JSON file.
     *
     * @param  string  $path  The full path to the JSON file.
     * @return self|null The loaded data or null if the file doesn't exist or is invalid.
     */
    public static function loadFromFile(string $path): ?self
    {
        if (! File::exists($path) || ! File::isFile($path)) {
            return null;
        }

        $content = File::get($path);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($data)) {
            return null;
        }

        return self::from($data);
    }

    /**
     * Persists the current relationship data to a JSON file.
     *
     * @param  string  $path  The full path where the JSON should be saved.
     * @return bool True on success, false otherwise.
     */
    public function saveToFile(string $path): bool
    {
        $json = $this->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return (bool) File::put($path, $json);
    }

    /**
     * Validates that the current model implementation matches the stored relationship data.
     *
     * It compares types, related models, foreign keys, and pivot tables.
     *
     * @param  array<string, array<string, array<string, mixed>>>  $currentModels  The implementation data discovered by scanning.
     * @param  bool  $strict  If true, it also warns about items in implementation missing in JSON.
     * @return array<string> List of error messages.
     */
    public function validateImplementation(array $currentModels, bool $strict = false): array
    {
        return (new RelationshipValidator($this->models))->validateImplementation($currentModels, $strict);
    }

    /**
     * Checks for potential issues in the relationship data.
     *
     * It identifies:
     * 1. Asymmetry (e.g., belongsToMany without an inverse).
     * 2. Broken relations (missing related model or non-existent class).
     * 3. Missing ID columns in database tables that look like foreign keys.
     *
     * @param  array<string, string[]>  $potentialRelations  Suspicious database columns discovered by scanner.
     * @return string[] List of warning messages.
     */
    public function getWarnings(array $potentialRelations = []): array
    {
        return (new RelationshipValidator($this->models))->getWarnings($potentialRelations);
    }

    /**
     * Prepares relationship data for diagram generation.
     *
     * It maps model names to their plural snake_case table names and determines the
     * relationship links between them.
     *
     * @param  bool|null  $withExtraPivotTables  If true, pivot tables are included as separate nodes.
     * @return array<string, array<string|null, EloquentRelation>> A map of table connections.
     */
    public function getDiagramData(?bool $withExtraPivotTables = null): array
    {
        return (new DiagramGenerator($this->models, $this->config))->getDiagramData($withExtraPivotTables);
    }

    /**
     * Summarizes relationship information for each model.
     *
     * It distinguishes between direct (e.g., belongsTo) and indirect (e.g., hasManyThrough) relationships.
     *
     * @return array<string, array{direct: string[], indirect: string[]}>
     */
    public function getModelRelationsData(): array
    {
        return (new DiagramGenerator($this->models, $this->config))->getModelRelationsData();
    }

    /**
     * Infers database tables and their expected foreign key columns from the relationship data.
     *
     * This is useful for generating documentation or verifying database schema.
     *
     * @return array<string, string[]> A mapping of table names to their identified foreign key columns.
     */
    public function getDatabaseTableData(): array
    {
        return (new DiagramGenerator($this->models, $this->config))->getDatabaseTableData();
    }

    /**
     * Find reverse relations for a given relation.
     *
     * @return array<int, array{0: string, 1: string}>
     */
    public function getReverseRelations(string $modelName, string $methodName): array
    {
        return (new DiagramGenerator($this->models, $this->config))->getReverseRelations($modelName, $methodName);
    }

    /**
     * For a morphTo relation, find all models that morph to it.
     *
     * @return string[] List of model names
     */
    public function getMorphToTargets(string $modelName, string $methodName): array
    {
        return (new DiagramGenerator($this->models, $this->config))->getMorphToTargets($modelName, $methodName);
    }
}
