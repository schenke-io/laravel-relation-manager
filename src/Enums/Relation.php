<?php

namespace SchenkeIo\LaravelRelationManager\Enums;

use ArchTech\Enums\From;
use Illuminate\Database\Eloquent\Relations as EloquentRelations;
use Illuminate\Support\Str;
use SchenkeIo\LaravelRelationManager\Data\ClassData;

enum Relation
{
    use From;

    case noRelation;
    case hasOne;  // inverse:  belongsTo
    case hasMany;  // inverse: belongsTo
    case hasOneThrough;
    case hasManyThrough;
    case belongsToMany; // inverse belongsToMany
    case belongsTo;
    case isSingle;

    case morphTo;
    case morphOne;
    case morphMany;
    case morphToMany;

    case morphedByMany; // returns as MorphToMany, so not visble, is used in tha Moprh class
    /*
     * when a relation is made with extra filter, like hasOneThrough, it has not any table keys
     */
    case hasOneIndirect;

    public static function fromRelationName(string $relationName): self
    {
        return match ($relationName) {
            'BelongsTo' => self::belongsTo,
            'BelongsToMany' => self::belongsToMany,
            'HasMany' => self::hasMany,
            'HasManyThrough' => self::hasManyThrough,
            'HasOne' => self::hasOne,
            'HasOneThrough' => self::hasOneThrough,
            'MorphMany' => self::morphMany,
            'MorphOne' => self::morphOne,
            'MorphTo' => self::morphTo,
            'MorphToMany' => self::morphToMany,
            default => self::noRelation
        };
    }

    public function getAssertName(): string
    {
        return 'assertModel'.ucfirst($this->name);
    }

    public function askForInverse(): bool
    {
        return self::inverse() != self::noRelation;
    }

    public function askForRelatedModel(): bool
    {
        return match ($this) {
            self::hasOne, self::hasMany, self::hasOneIndirect,
            self::hasOneThrough, self::hasManyThrough,
            self::belongsToMany,
            self::morphOne, self::morphMany, self::morphToMany, self::morphedByMany => true,
            default => false
        };
    }

    public function inverse(bool $preventInverse = false): self
    {
        if ($preventInverse) {
            return self::noRelation;
        } else {
            return match ($this) {
                self::hasOne, self::hasMany => self::belongsTo,
                self::belongsToMany => self::belongsToMany,
                self::morphOne, self::morphMany => self::morphTo,
                self::morphToMany => self::morphedByMany,
                default => self::noRelation
            };
        }
    }

    public function hasInverse(bool $preventInverse = false): bool
    {
        return $this->inverse($preventInverse) !== self::noRelation;
    }

    /**
     * can the relation be defined in a command
     */
    public function hasPublicFunction(): bool
    {
        return match ($this) {
            self::isSingle,
            self::belongsTo,
            self::morphTo,
            self::noRelation => false,
            default => true
        };
    }

    public function isRelation(): bool
    {
        return match ($this) {
            self::isSingle, self::noRelation => false,
            default => true
        };

    }

    public function isDirectRelation(): bool
    {
        return match ($this) {
            self::hasOne, self::hasMany,
            self::belongsTo,
            //            self::morphOne,
            self::morphToMany => true,
            default => false
        };
    }

    public function getClass(): ?string
    {
        return match ($this) {
            self::hasOne, self::hasOneIndirect => EloquentRelations\HasOne::class,
            self::hasMany => EloquentRelations\HasMany::class,
            self::belongsToMany => EloquentRelations\BelongsToMany::class,
            self::hasOneThrough => EloquentRelations\HasOneThrough::class,
            self::hasManyThrough => EloquentRelations\HasManyThrough::class,
            self::belongsTo => EloquentRelations\BelongsTo::class,
            self::morphTo => EloquentRelations\MorphTo::class,
            self::morphOne => EloquentRelations\MorphOne::class,
            self::morphToMany => EloquentRelations\MorphToMany::class,
            self::morphMany => EloquentRelations\MorphMany::class,
            default => null,
        };
    }

    /**
     * @param  array<string, array<string|null, Relation>>  $tables
     */
    public function setTableLinks(
        string $modelName1,
        string $modelName2,
        array &$tables,
        bool $withExtraPivotTables): void
    {
        $modelName1 = ClassData::take($modelName1)->getShortName();
        $modelName2 = ClassData::take($modelName2)->getShortName();

        $tableName1 = Str::snake(Str::plural($modelName1));
        $tableName2 = Str::snake(Str::plural($modelName2));

        $names = [Str::snake($modelName1), Str::snake($modelName2)];
        sort($names);
        $pivotTable = implode('_', $names);
        $morphPivotTable = Str::snake($modelName2).'able';
        switch ($this) {
            case self::hasOne:
            case self::hasMany:
            case self::morphOne:
            case self::morphMany:
                $tables[$tableName2][$tableName1] = $this;
                break;
            case self::hasOneThrough:
            case self::hasManyThrough:
            case self::hasOneIndirect:
                // no link
                break;
            case self::belongsToMany:

                if ($withExtraPivotTables) {
                    $tables[$pivotTable][$tableName1] = $this;
                    $tables[$pivotTable][$tableName2] = $this;
                } else {
                    /*
                     * it is called from both ends of the relationship so
                     * we decide to use it only once here
                     */
                    if ($tableName1 > $tableName2) {
                        $tables[$tableName1][$tableName2] = $this;
                    }
                }
                break;
            case self::morphToMany:
                // todo: morphToMany
                break;

            case self::belongsTo:
                $tables[$tableName1][$tableName2] = $this;
                break;
            case self::morphTo:
            case self::isSingle:
            case self::noRelation:
                $tables[$tableName1][null] = $this;
                break;
        }
    }

    public function isMorph(): bool
    {
        return match ($this) {
            self::morphTo, self::morphOne, self::morphMany, self::morphToMany => true,
            default => false
        };
    }

    public function isDoubleLine(): bool
    {
        return match ($this) {
            self::morphToMany, self::belongsToMany => true,
            default => false
        };
    }
}
