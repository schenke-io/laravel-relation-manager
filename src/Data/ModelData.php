<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use Spatie\LaravelData\Data;

class ModelData extends Data
{
    public function __construct(
        /** @var array<string, RelationData> */
        public readonly array $methods = []
    ) {}
}
