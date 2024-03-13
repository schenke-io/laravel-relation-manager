<?php

namespace SchenkeIo\LaravelRelationManager\Define;

use BackedEnum;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Support\Str;
use SchenkeIo\LaravelRelationManager\Data\ClassData;

enum RelationsEnum
{
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
     * ENUM as integrer
     */
    case castEnum;
    case castEnumReverse;

    /*
     * standard  speak
     */
    case isManyToMany;

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
            self::hasOne, self::hasMany,
            self::isManyToMany,
            self::castEnum,
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
            self::castEnumReverse,
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
            self::morphOne, self::morphMany,
            self::castEnum, self::castEnumReverse => true,
            default => false
        };
    }

    /**
     * @throws \Exception
     */
    public function getClass(): ?string
    {
        return match ($this) {
            self::hasOne => Relations\HasOne::class,
            self::hasMany => Relations\HasMany::class,
            self::belongsToMany, self::isManyToMany => Relations\BelongsToMany::class,
            self::hasOneThrough => Relations\HasOneThrough::class,
            self::hasManyThrough => Relations\HasManyThrough::class,
            self::belongsTo => Relations\BelongsTo::class,
            self::morphTo => Relations\MorphTo::class,
            self::morphOne => Relations\MorphOne::class,
            self::morphMany => Relations\MorphMany::class,
            self::castEnum => BackedEnum::class,
            default => throw new \Exception('class unknown for '.$this->name)
        };
    }

    public function setTableLinks(string $modelName1, string $modelName2, array &$tables): void
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
                // no link
                break;
            case self::belongsToMany:
            case self::isManyToMany:
                $tables[$tableName3][$tableName1] = $this;
                $tables[$tableName3][$tableName2] = $this;
                break;
            case self::belongsTo:
            case self::castEnum:
                $tables[$tableName1][$tableName2] = $this;
                break;
            case self::morphTo:
            case self::isSingle:
            case self::noRelation:
            case self::castEnumReverse:
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
}
