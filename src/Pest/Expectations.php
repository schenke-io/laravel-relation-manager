<?php

namespace SchenkeIo\LaravelRelationManager\Pest;

use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;

/**
 * Pest expectations for the Laravel Relation Manager, providing
 * custom assertions for model relationships.
 */

/* @phpstan-ignore-next-line */
expect()->extend('toHaveRelation', fn (string $relatedModel, EloquentRelation $relation) => ExpectationHandlers::toHaveRelation($this, $relatedModel, $relation));

/* @phpstan-ignore-next-line */
expect()->extend('toHasOne', fn (string $relatedModel) => $this->toHaveRelation($relatedModel, EloquentRelation::hasOne));

/* @phpstan-ignore-next-line */
expect()->extend('toHasMany', fn (string $relatedModel) => $this->toHaveRelation($relatedModel, EloquentRelation::hasMany));

/* @phpstan-ignore-next-line */
expect()->extend('toBelongsTo', fn (string $relatedModel) => $this->toHaveRelation($relatedModel, EloquentRelation::belongsTo));

/* @phpstan-ignore-next-line */
expect()->extend('toBelongsToMany', fn (string $relatedModel) => $this->toHaveRelation($relatedModel, EloquentRelation::belongsToMany));
