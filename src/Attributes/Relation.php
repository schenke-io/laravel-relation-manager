<?php

namespace SchenkeIo\LaravelRelationManager\Attributes;

use Attribute;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation as RelationEnum;

/**
 * Attribute to define a relationship on a model or method.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
readonly class Relation
{
    /**
     * @param  RelationEnum  $type  The type of relationship (e.g., hasMany, belongsTo).
     * @param  string|null  $related  The fully qualified class name of the related model.
     * @param  bool  $addReverse  If true, the scanner will automatically add the inverse relation to the related model.
     */
    public function __construct(
        public RelationEnum $type,
        public ?string $related = null,
        public bool $addReverse = false
    ) {}
}
