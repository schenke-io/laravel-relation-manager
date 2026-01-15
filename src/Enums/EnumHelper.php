<?php

namespace SchenkeIo\LaravelRelationManager\Enums;

/**
 * Trait providing helper methods for Enums, such as finding a case by name.
 */
trait EnumHelper
{
    public static function from(string $name): self
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }
        throw new \ValueError("$name is not a valid case for enum ".self::class);
    }

    public static function tryFrom(string $name): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }
}
