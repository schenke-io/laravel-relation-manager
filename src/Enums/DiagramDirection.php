<?php

namespace SchenkeIo\LaravelRelationManager\Enums;

enum DiagramDirection
{
    case LR;
    case TD;

    public static function fromBool(bool $diagrammDirectionTd): self
    {
        return $diagrammDirectionTd ? self::TD : self::LR;
    }
}
