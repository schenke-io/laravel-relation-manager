<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use Spatie\LaravelData\Data;

/**
 * Data Transfer Object representing a single Model and its relationships.
 */
class ModelData extends Data
{
    public function __construct(
        /** @var array<string, RelationData> */
        public readonly array $methods = []
    ) {}
}
