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

    public function belongsToMany(bool $addReverseRelation): DefineRelation
    {
        return $this->buildRelation(
            "",
            $addReverseRelation,
            RelationsEnum::belongsToMany,
            RelationsEnum::belongsToMany
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