<?php

namespace SchenkeIo\LaravelRelationManager\Enums;

/**
 * Enum defining the direction of the Mermaid diagram (Left-to-Right or Top-Down).
 */
enum DiagramDirection
{
    case LR;
    case TD;

    public static function fromBool(bool $diagramDirectionTd): self
    {
        return $diagramDirectionTd ? self::TD : self::LR;
    }
}
