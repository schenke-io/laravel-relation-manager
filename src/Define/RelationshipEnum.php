<?php

namespace SchenkeIo\LaravelRelationshipManager\Define;

use Illuminate\Database\Eloquent\Relations;

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
     * standard speak
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
            self::hasOne, self::hasMany => true,
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
        return self::noRelation == $this->getInverse($preventInverse);
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
            self::noRelation, self::isOneToOne, self::isOneToMany, self::isManyToMany => false,
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
}
