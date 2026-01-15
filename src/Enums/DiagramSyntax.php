<?php

namespace SchenkeIo\LaravelRelationManager\Enums;

/**
 * Enum defining the output syntax for diagrams (currently Mermaid and Dot).
 */
enum DiagramSyntax
{
    case Mermaid;
    case Dot;

    public function start(DiagramDirection $diagramDirection): string
    {
        $direction = $diagramDirection->name;

        return match ($this) {
            DiagramSyntax::Mermaid => "```mermaid\nflowchart $direction\n",
            DiagramSyntax::Dot => "digraph  {\n  rankdir=$direction;\n",
        };
    }

    public function morph(int|string $table1, int|string $table2): string
    {
        return match ($this) {
            DiagramSyntax::Mermaid => "$table1 -.-> $table2\n",
            DiagramSyntax::Dot => "  $table1 -> $table2;\n"
        };
    }

    public function normal(int|string $table1, int|string $table2): string
    {
        return match ($this) {
            DiagramSyntax::Mermaid => "$table1 -.-> $table2\n",
            DiagramSyntax::Dot => "  $table1 -> $table2 [style=bold];\n",
        };
    }

    public function double(int|string $table1, int|string $table2): string
    {
        return match ($this) {
            DiagramSyntax::Mermaid => "$table1 <==> $table2\n",
            DiagramSyntax::Dot => "  $table1 -> $table2 [dir=none, color=silver, penwidth=8];\n",
        };
    }

    public function end(): string
    {
        return match ($this) {
            DiagramSyntax::Mermaid => "\n```\n",
            DiagramSyntax::Dot => "}\n"
        };
    }
}
