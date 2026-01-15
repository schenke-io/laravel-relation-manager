<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use SchenkeIo\LaravelRelationManager\Enums\Relation;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class RelationData extends Data
{
    public function __construct(
        public readonly Relation $type,
        public readonly ?string $related = null,
        public readonly ?string $pivotTable = null,
        public readonly ?string $foreignKey = null,
    ) {}
}
