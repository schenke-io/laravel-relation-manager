<?php

namespace SchenkeIo\LaravelRelationManager\Enums;

use Illuminate\Database\Eloquent\Relations as EloquentRelations;

/**
 * Enum representing various Laravel relationship types and providing
 * utility methods for inverse relationships, syntax mapping, and more.
 *
 * This enum captures the different kinds of Eloquent relationships supported by
 * Laravel, including one-to-one, one-to-many, many-to-many, and polymorphic
 * variants. It provides logic to determine the logical inverse of a relation,
 * identify if a relation is direct or indirect, and map relation types to
 * their corresponding Laravel Eloquent relation classes.
 */
enum EloquentRelation: string
{
    use EnumHelper;

    case noRelation = 'noRelation';
    case hasOne = 'hasOne';  // inverse:  belongsTo
    case hasMany = 'hasMany';  // inverse: belongsTo
    case hasOneThrough = 'hasOneThrough';
    case hasManyThrough = 'hasManyThrough';
    case belongsToMany = 'belongsToMany'; // inverse belongsToMany
    case belongsTo = 'belongsTo';
    case isSingle = 'isSingle';

    case morphTo = 'morphTo';
    case morphOne = 'morphOne';
    case morphMany = 'morphMany';
    case morphToMany = 'morphToMany';

    case morphedByMany = 'morphedByMany'; // returns as MorphToMany, so not visble, is used in tha Moprh class

    case bidirectional = 'bidirectional';

    /*
     * when a relation is made with an extra filter, like hasOneThrough, it has not any table keys
     */
    case hasOneIndirect = 'hasOneIndirect';

    /**
     * Map a short relation class name (e.g., 'HasMany') to its corresponding Enum case.
     *
     * @param  string  $relationName  The short class name of the relation.
     */
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

    /**
     * Generates the PHPUnit assertion method name for this relation type.
     */
    public function getAssertName(): string
    {
        return 'assertModel'.ucfirst($this->name);
    }

    /**
     * Checks if this relation type typically has a logical inverse.
     */
    public function askForInverse(): bool
    {
        return self::inverse() != self::noRelation;
    }

    /**
     * Determines if this relation type requires a related model to be defined.
     */
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

    /**
     * Get the inverse relationship type for the current relation.
     *
     * @param  bool  $preventInverse  If true, returns noRelation.
     */
    public function inverse(bool $preventInverse = false): self
    {
        if ($preventInverse) {
            return self::noRelation;
        } else {
            return match ($this) {
                self::hasOne, self::hasMany => self::belongsTo,
                self::belongsTo => self::hasMany, // default to hasMany
                self::belongsToMany => self::belongsToMany,
                self::morphOne, self::morphMany => self::morphTo,
                self::morphTo => self::morphMany, // default to morphMany
                self::morphToMany => self::morphedByMany,
                self::morphedByMany => self::morphToMany,
                default => self::noRelation
            };
        }
    }

    /**
     * Convenience method to check if an inverse exists.
     */
    public function hasInverse(bool $preventInverse = false): bool
    {
        return $this->inverse($preventInverse) !== self::noRelation;
    }

    /**
     * Checks if this relation can be directly initiated via a public method in a model definition context.
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

    /**
     * Distinguishes actual relationships from internal state enums like noRelation.
     */
    public function isRelation(): bool
    {
        return match ($this) {
            self::isSingle, self::noRelation => false,
            default => true
        };

    }

    /**
     * Identifies relationships that represent a direct database link (e.g., via a foreign key).
     */
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

    /**
     * Maps the enum case to the fully qualified class name of the Eloquent relation.
     */
    public function getClass(): ?string
    {
        return match ($this) {
            self::hasOne, self::hasOneIndirect => EloquentRelations\HasOne::class,
            self::hasMany => EloquentRelations\HasMany::class,
            self::belongsToMany, self::morphedByMany => EloquentRelations\BelongsToMany::class,
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
     * Identifies polymorphic relationship types.
     */
    public function isMorph(): bool
    {
        return match ($this) {
            self::morphTo, self::morphOne, self::morphMany, self::morphToMany, self::morphedByMany => true,
            default => false
        };
    }

    /**
     * Indicates if the relationship should be represented by a double line in diagrams.
     */
    public function isDoubleLine(): bool
    {
        return match ($this) {
            self::morphToMany, self::belongsToMany, self::bidirectional => true,
            default => false
        };
    }
}
