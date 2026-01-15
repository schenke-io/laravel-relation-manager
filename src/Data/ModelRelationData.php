<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use Illuminate\Support\Str;
use SchenkeIo\LaravelRelationManager\Enums\Relation;
use Spatie\LaravelData\Data;

/**
 * Data object representing a relationship between two models,
 * including keys and comments. Used for verification and testing.
 */
class ModelRelationData extends Data
{
    public readonly ?string $key1;

    public readonly ?string $key2;

    public readonly ?string $comment;

    public function __construct(
        public readonly string $model1,
        public readonly ?string $model2,
        public readonly Relation $relation = Relation::noRelation,
        public readonly bool $preventInverse = false,
        ?string $key1 = null,
        ?string $key2 = null,
        ?string $comment = null
    ) {
        if ($relation->askForRelatedModel()) {
            $this->key1 = $key1 ?? 'id';
            $this->key2 = $this->getDefaultForeignKey($model1);
        } else {
            $this->key1 = $this->getDefaultForeignKey($model2 ?? '');
            $this->key2 = $key2 ?? 'id';
        }
        $this->comment = $comment;
    }

    protected function getDefaultForeignKey(string $className): string
    {
        $baseName = basename(str_replace('\\', '/', $className));

        return Str::snake($baseName).'_id';
    }
}
