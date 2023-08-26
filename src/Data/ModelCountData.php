<?php

namespace SchenkeIo\LaravelRelationshipManager\Data;

use Spatie\LaravelData\Data;

class ModelCountData extends Data
{
    public function __construct(
        public readonly string $model,
        public readonly int $count
    ) {
    }
}
