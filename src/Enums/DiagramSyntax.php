<?php

namespace SchenkeIo\LaravelRelationManager\Enums;

enum DiagramSyntax
{
    case Mermaid;
    case Dot;

    public function start(DiagramDirection $diagrammDirection): string
    {
        $direction = $diagrammDirection->name;

        return match ($this) {
            DiagramSyntax::Mermaid => "```mermaid\nflowchart $direction\n",
            DiagramSyntax::Dot => "digraph  {\n  rankdir=$direction;\n",
        };
    }

    public function morph(int|string $table1, int|string $table2)
    {
        return match ($this) {
            DiagramSyntax::Mermaid => "$table1 --> $table2\n",
            DiagramSyntax::Dot => "  $table1 -> $table2;\n"
        };
    }

    public function normal(int|string $table1, int|string $table2)
    {
        return match ($this) {
            DiagramSyntax::Mermaid => "$table1 ==> $table2\n",
            DiagramSyntax::Dot => "  $table1 -> $table2 [style=bold];\n",
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
