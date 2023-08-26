<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use SchenkeIo\LaravelRelationManager\Define\RelationshipEnum;
use Spatie\LaravelData\Data;

class ModelRelationData extends Data
{
    public function __construct(
        public readonly string $model1,
        public readonly ?string $model2,
        public readonly RelationshipEnum $relation,
        public readonly bool $noInverse
    ) {
    }
}
