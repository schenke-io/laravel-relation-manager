<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * Data Transfer Object representing a single relationship definition.
 */
#[MapName(SnakeCaseMapper::class)]
class RelationData extends Data
{
    public function __construct(
        public readonly EloquentRelation $type,
        public readonly ?string $related = null,
        public readonly ?string $pivotTable = null,
        public readonly ?string $foreignKey = null,
    ) {}
}
