<?php

namespace SchenkeIo\LaravelRelationManager\Enums;

enum Type
{
    case Boolean;
    case String;

    public function format(mixed $value): string|bool
    {
        return match ($this) {
            self::String => (string) $value,
            self::Boolean => (bool) $value,
        };
    }
}
