<?php

namespace SchenkeIo\LaravelRelationManager\Pest;

use SchenkeIo\LaravelRelationManager\Enums\Relation;

/**
 * Pest expectations for the Laravel Relation Manager, providing
 * custom assertions for model relationships.
 */

/* @phpstan-ignore-next-line */
expect()->extend('toHaveRelation', fn (string $relatedModel, Relation $relation) => ExpectationHandlers::toHaveRelation($this, $relatedModel, $relation));

/* @phpstan-ignore-next-line */
expect()->extend('toHasOne', fn (string $relatedModel) => $this->toHaveRelation($relatedModel, Relation::hasOne));

/* @phpstan-ignore-next-line */
expect()->extend('toHasMany', fn (string $relatedModel) => $this->toHaveRelation($relatedModel, Relation::hasMany));

/* @phpstan-ignore-next-line */
expect()->extend('toBelongsTo', fn (string $relatedModel) => $this->toHaveRelation($relatedModel, Relation::belongsTo));

/* @phpstan-ignore-next-line */
expect()->extend('toBelongsToMany', fn (string $relatedModel) => $this->toHaveRelation($relatedModel, Relation::belongsToMany));
