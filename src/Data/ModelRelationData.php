<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use Illuminate\Support\Str;
use SchenkeIo\LaravelRelationManager\Define\RelationsEnum;
use Spatie\LaravelData\Data;

/**
 * used in Phpunit/Constraints
 */
class ModelRelationData extends Data
{
    public function __construct(
        public readonly string $model1,
        public readonly ?string $model2,
        public readonly RelationsEnum $relation = RelationsEnum::noRelation,
        public readonly bool $preventInverse = false,
        public ?string $key1 = null,
        public ?string $key2 = null,
    ) {
        if ($relation->askForRelatedModel()) {
            $this->key1 = $key1 ?? 'id';
            $this->key2 = $this->getDefaultForeignKey($model1);
        } else {
            $this->key1 = $this->getDefaultForeignKey($model2 ?? '');
            $this->key2 = $key2 ?? 'id';
        }
    }

    protected function getDefaultForeignKey(string $className): string
    {
        $baseName = basename(str_replace('\\', '/', $className));

        return Str::snake($baseName).'_id';
    }
}
