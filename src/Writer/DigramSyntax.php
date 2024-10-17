<?php

namespace SchenkeIo\LaravelRelationManager\Writer;

use Illuminate\Support\Str;

enum DigramSyntax
{
    case Mermaid;
    case Dot;

    public function start(DiagramDirection $diagrammDirection): string
    {
        $direction = $diagrammDirection->name;

        return match ($this) {
            DigramSyntax::Mermaid => "```mermaid\nflowchart $direction\n",
            DigramSyntax::Dot => "digraph  {\n  rankdir=$direction;\n",
        };
    }

    public function enum(string $table1, string $table2): string
    {

        $enum = ucfirst(Str::camel(Str::singular($table2)));

        return match ($this) {
            DigramSyntax::Mermaid => "$table1 -.-> $enum\n$enum([$enum])\n    style $enum fill:silver;\n",
            DigramSyntax::Dot => "  $table1 -> $table2 [style=dotted];\n",
        };
    }

    public function morph(int|string $table1, int|string $table2)
    {
        return match ($this) {
            DigramSyntax::Mermaid => "$table1 --> $table2\n",
            DigramSyntax::Dot => "  $table1 -> $table2;\n"
        };
    }

    public function normal(int|string $table1, int|string $table2)
    {
        return match ($this) {
            DigramSyntax::Mermaid => "$table1 ==> $table2\n",
            DigramSyntax::Dot => "  $table1 -> $table2 [style=bold];\n",
        };
    }

    public function end(): string
    {
        return match ($this) {
            DigramSyntax::Mermaid => "\n```\n",
            DigramSyntax::Dot => "}\n"
        };
    }
}
