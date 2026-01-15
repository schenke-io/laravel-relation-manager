<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use Spatie\LaravelData\Data;

/**
 * Data object to store the count of a specific model,
 * primarily used within PHPUnit constraints for testing.
 */
class ModelCountData extends Data
{
    public function __construct(
        public readonly string $model,
        public readonly int $count
    ) {}
}
