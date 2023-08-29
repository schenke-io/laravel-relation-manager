<?php

namespace SchenkeIo\LaravelRelationManager\Define;

use Illuminate\Database\Eloquent\Relations;
use Illuminate\Support\Str;

enum RelationshipEnum
{
    case noRelation;
    case hasOne;  // inverse:  belongsTo
    case hasMany;  // inverse: belongsTo
    case hasOneThrough;
    case hasManyThrough;
    case belongsToMany; // inverse belongsToMany
    case belongsTo;
    case isSingle;

    /*
     * standard  speak
     */
    case isOneToOne;
    case isOneToMany;
    case isManyToMany;

    public function getAssertName(): string
    {
        return 'assertModel'.ucfirst($this->name);
    }

    public function askForInverse(): bool
    {
        return match ($this) {
            self::hasOne, self::hasMany, self::isOneToOne, self::isOneToMany => true,
            default => false
        };
    }

    public function getInverse(bool $preventInverse = false): self
    {
        if ($preventInverse) {
            return self::noRelation;
        } else {
            return match ($this) {
                self::hasOne, self::hasMany, self::isOneToOne, self::isOneToMany => self::belongsTo,
                self::belongsToMany, self::isManyToMany => self::belongsToMany,
                default => self::noRelation
            };
        }
    }

    public function hasInverse(bool $preventInverse = false): bool
    {
        return self::noRelation !== $this->getInverse($preventInverse);
    }

    public function hasPublicFunction(): bool
    {
        return match ($this) {
            self::belongsTo, self::noRelation => false,
            default => true
        };
    }

    public function hasAssertFunction(): bool
    {
        return match ($this) {
            self::noRelation, self::isSingle => false,
            default => true
        };
    }

    public function askForModel(): bool
    {
        return match ($this) {
            self::isSingle, self::noRelation => false,
            default => true
        };
    }

    /**
     * @throws \Exception
     */
    public function getClass(): ?string
    {
        return match ($this) {
            self::hasOne, self::isOneToOne => Relations\HasOne::class,
            self::hasMany, self::isOneToMany => Relations\HasMany::class,
            self::belongsToMany, self::isManyToMany => Relations\BelongsToMany::class,
            self::hasOneThrough => Relations\HasOneThrough::class,
            self::hasManyThrough => Relations\HasManyThrough::class,
            self::belongsTo => Relations\BelongsTo::class,
            default => throw new \Exception('class unknown for '.$this->name)
        };
    }

    /**
     *   the arrow points from the field "[model]_id to the model.id field
     */
    public function getMermaidLine(string $modelName1, string $modelName2): string
    {
        $tableName1 = Str::snake(Str::plural($modelName1));
        $tableName2 = Str::snake(Str::plural($modelName2));
        $names = [Str::snake($modelName1), Str::snake($modelName2)];
        sort($names);
        $tableName3 = implode('_', $names);

        return match ($this) {
            self::isOneToOne,
            self::isOneToMany,
            self::hasOne,
            self::hasMany => "$tableName2 ---> $tableName1\n",
            self::hasOneThrough,
            self::hasManyThrough => "$tableName2-- through ---$tableName1\n",

            self::isSingle => "$tableName1\n",

            self::belongsToMany,
            self::isManyToMany => "$tableName3 ---> $tableName1\n$tableName3 ---> $tableName2\n",

            default => ''
        };

    }
}
