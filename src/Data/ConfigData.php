<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * Type-safe configuration for the Relation Manager.
 */
#[MapName(SnakeCaseMapper::class)]
class ConfigData extends Data
{
    /**
     * The path where the RELATIONS.md file will be generated.
     */
    public readonly string $markdownPath;

    /**
     * The directory where Eloquent models are located (e.g., 'app/Models').
     */
    public readonly string $modelPath;

    /**
     * Whether to use Mermaid for diagram generation.
     */
    public readonly bool $useMermaid;

    public function __construct(
        ?string $markdownPath = null,
        ?string $modelPath = null,
        ?bool $useMermaid = null,
    ) {
        $this->markdownPath = $markdownPath ?: base_path('RELATIONS.md');
        $this->modelPath = $modelPath ?: 'app/Models';
        $this->useMermaid = $useMermaid ?? true;
    }
}
