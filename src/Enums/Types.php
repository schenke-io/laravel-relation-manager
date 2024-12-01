<?php

namespace SchenkeIo\LaravelRelationManager\Enums;

enum Types
{
    case Boolean;
    case String;

    public function format(mixed $value): mixed
    {
        return match ($this) {
            self::String => (string) $value,
            self::Boolean => (bool) $value,
        };
    }
}
