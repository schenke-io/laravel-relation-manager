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

    /*
     * standard  speak
     */
    case isManyToMany;

    /*
     * when a relation is made with extra filter, like hasOneThrough, it has not table keys
     */
    case hasOneIndirect;

    public static function tryFromRelationName(string $relationName): ?self
    {
        return self::tryFromName(lcfirst($relationName));
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
            self::isManyToMany,
            self::hasOneThrough, self::hasManyThrough,
            self::morphOne, self::morphMany => true,
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
                self::isManyToMany => self::belongsToMany,
                self::morphOne, self::morphMany => self::morphTo,
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
            self::belongsToMany,
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
            self::morphOne, self::morphMany => true,
            default => false
        };
    }

    /**
     * @throws \Exception
     */
    public function getClass(): string
    {
        return match ($this) {
            self::hasOne, self::hasOneIndirect => EloquentRelations\HasOne::class,
            self::hasMany => EloquentRelations\HasMany::class,
            self::belongsToMany, self::isManyToMany => EloquentRelations\BelongsToMany::class,
            self::hasOneThrough => EloquentRelations\HasOneThrough::class,
            self::hasManyThrough => EloquentRelations\HasManyThrough::class,
            self::belongsTo => EloquentRelations\BelongsTo::class,
            self::morphTo => EloquentRelations\MorphTo::class,
            self::morphOne => EloquentRelations\MorphOne::class,
            self::morphMany => EloquentRelations\MorphMany::class,
            default => throw new \Exception('class unknown for '.$this->name)
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
        $tableName3 = implode('_', $names);
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
            case self::isManyToMany:
                if ($withExtraPivotTables) {
                    $tables[$tableName3][$tableName1] = $this;
                    $tables[$tableName3][$tableName2] = $this;
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
            self::morphOne, self::morphMany => true,
            default => false
        };
    }

    public function isDouble(): bool
    {
        return match ($this) {
            self::isManyToMany, self::belongsToMany => true,
            default => false
        };
    }
}
