<?php

/**
 * ## Description of all the possible relationships
 *
 * ------
 *
 * This file is auto-generated by:
 * SchenkeIo\LaravelRelationManager\Writer\GenerateRelationshipsForPrimaryModelTrait
 * using the data from: SchenkeIo\LaravelRelationManager\Define\RelationshipEnum
 */

namespace SchenkeIo\LaravelRelationManager\Define;

use SchenkeIo\LaravelRelationManager\Data\ModelRelationData;

trait RelationshipsForPrimaryModel
{
    public function hasOne(string $model, bool $preventInverse = false): ModelRelationData
    {
        return new ModelRelationData($this->model, $model, RelationshipEnum::hasOne, $preventInverse);
    }

    public function hasMany(string $model, bool $preventInverse = false): ModelRelationData
    {
        return new ModelRelationData($this->model, $model, RelationshipEnum::hasMany, $preventInverse);
    }

    public function hasOneThrough(string $model): ModelRelationData
    {
        return new ModelRelationData($this->model, $model, RelationshipEnum::hasOneThrough, false);
    }

    public function hasManyThrough(string $model): ModelRelationData
    {
        return new ModelRelationData($this->model, $model, RelationshipEnum::hasManyThrough, false);
    }

    public function belongsToMany(string $model): ModelRelationData
    {
        return new ModelRelationData($this->model, $model, RelationshipEnum::belongsToMany, false);
    }

    public function isSingle(): ModelRelationData
    {
        return new ModelRelationData($this->model, null, RelationshipEnum::isSingle, false);
    }

    public function isOneToOne(string $model): ModelRelationData
    {
        return new ModelRelationData($this->model, $model, RelationshipEnum::isOneToOne, false);
    }

    public function isOneToMany(string $model): ModelRelationData
    {
        return new ModelRelationData($this->model, $model, RelationshipEnum::isOneToMany, false);
    }

    public function isManyToMany(string $model): ModelRelationData
    {
        return new ModelRelationData($this->model, $model, RelationshipEnum::isManyToMany, false);
    }
}
