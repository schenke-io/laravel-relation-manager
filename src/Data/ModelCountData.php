<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use Spatie\LaravelData\Data;

class ModelCountData extends Data
{
    public function __construct(
        public readonly string $model,
        public readonly int $count
    ) {
    }
}
