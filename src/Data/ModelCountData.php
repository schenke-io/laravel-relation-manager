<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use Spatie\LaravelData\Data;

/**
 * used in Phpunit/Constraints
 */
class ModelCountData extends Data
{
    public function __construct(
        public readonly string $model,
        public readonly int $count
    ) {}
}
