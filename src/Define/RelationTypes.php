<?php

/**
 * ## Description of all the possible relationships
 *
 * ------
 *
 * This file is auto-generated by:
 * SchenkeIo\LaravelRelationManager\Writer\GenerateRelationTypesTrait
 * using the data from: SchenkeIo\LaravelRelationManager\Define\RelationsEnum
 */

namespace SchenkeIo\LaravelRelationManager\Define;

trait RelationTypes
{
    public function hasOne(string $modelName, bool $addReverseRelation): DefineRelation
    {
        return $this->buildRelation(
            $modelName,
            $addReverseRelation,
            RelationsEnum::hasOne,
            RelationsEnum::belongsTo
        );
    }

    public function hasMany(string $modelName, bool $addReverseRelation): DefineRelation
    {
        return $this->buildRelation(
            $modelName,
            $addReverseRelation,
            RelationsEnum::hasMany,
            RelationsEnum::belongsTo
        );
    }

    public function hasOneThrough(string $modelName): DefineRelation
    {
        return $this->buildRelation(
            $modelName,
            false,
            RelationsEnum::hasOneThrough,
            RelationsEnum::noRelation
        );
    }

    public function hasManyThrough(string $modelName): DefineRelation
    {
        return $this->buildRelation(
            $modelName,
            false,
            RelationsEnum::hasManyThrough,
            RelationsEnum::noRelation
        );
    }

    public function morphOne(string $modelName, bool $addReverseRelation): DefineRelation
    {
        return $this->buildRelation(
            $modelName,
            $addReverseRelation,
            RelationsEnum::morphOne,
            RelationsEnum::morphTo
        );
    }

    public function morphMany(string $modelName, bool $addReverseRelation): DefineRelation
    {
        return $this->buildRelation(
            $modelName,
            $addReverseRelation,
            RelationsEnum::morphMany,
            RelationsEnum::morphTo
        );
    }

    public function castEnum(string $modelName): DefineRelation
    {
        return $this->buildRelation(
            $modelName,
            false,
            RelationsEnum::castEnum,
            RelationsEnum::noRelation
        );
    }

    public function isManyToMany(string $modelName, bool $addReverseRelation): DefineRelation
    {
        return $this->buildRelation(
            $modelName,
            $addReverseRelation,
            RelationsEnum::isManyToMany,
            RelationsEnum::belongsToMany
        );
    }
}
