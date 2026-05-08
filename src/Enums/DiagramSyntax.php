<?php

namespace SchenkeIo\LaravelRelationManager\Enums;

/**
 * Enum defining the output syntax for diagrams (currently Mermaid and Dot).
 */
enum DiagramSyntax
{
    case Mermaid;
    case Dot;

    /**
     * Get the starting string for the diagram syntax.
     */
    public function start(DiagramDirection $diagramDirection): string
    {
        $direction = $diagramDirection->name;

        return match ($this) {
            DiagramSyntax::Mermaid => "```mermaid\nflowchart $direction\n",
            DiagramSyntax::Dot => "digraph  {\n  rankdir=$direction;\n",
        };
    }

    /**
     * Get the string for a polymorphic relationship.
     */
    public function morph(int|string $table1, int|string $table2): string
    {
        return match ($this) {
            DiagramSyntax::Mermaid => "$table1 -.-> $table2\n",
            DiagramSyntax::Dot => "  $table1 -> $table2;\n"
        };
    }

    /**
     * Get the string for a normal relationship.
     */
    public function normal(int|string $table1, int|string $table2): string
    {
        return match ($this) {
            DiagramSyntax::Mermaid => "$table1 -.-> $table2\n",
            DiagramSyntax::Dot => "  $table1 -> $table2 [style=bold];\n",
        };
    }

    /**
     * Get the string for a many-to-many (double) relationship.
     */
    public function double(int|string $table1, int|string $table2): string
    {
        return match ($this) {
            DiagramSyntax::Mermaid => "$table1 <==> $table2\n",
            DiagramSyntax::Dot => "  $table1 -> $table2 [dir=none, color=silver, penwidth=8];\n",
        };
    }

    /**
     * Get the ending string for the diagram syntax.
     */
    public function end(): string
    {
        return match ($this) {
            DiagramSyntax::Mermaid => "\n```\n",
            DiagramSyntax::Dot => "}\n"
        };
    }
}
