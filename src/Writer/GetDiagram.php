<?php

namespace SchenkeIo\LaravelRelationManager\Writer;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use SchenkeIo\LaravelRelationManager\Enums\DiagramDirection;
use SchenkeIo\LaravelRelationManager\Enums\DiagramSyntax;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;

/**
 * Service to generate diagram code (Mermaid, Graphviz) from relationship data.
 */
readonly class GetDiagram
{
    public const GRAPHVIZ_BASENAME = 'diagram';

    /**
     * @param  array<string, array<string|null, EloquentRelation>>  $databaseData
     */
    public static function getMermaidCode(array $databaseData, DiagramDirection $diagramDirection): string
    {
        return self::getDiagramCode(DiagramSyntax::Mermaid, $databaseData, $diagramDirection);
    }

    public static function getGraphvizCode(): string
    {
        return "<img src='".self::GRAPHVIZ_BASENAME.".png' alt='".self::GRAPHVIZ_BASENAME."' />";
    }

    /**
     * @param  array<string, array<string|null, EloquentRelation>>  $databaseData
     */
    public static function writeGraphvizFile(
        array $databaseData,
        DiagramDirection $diagramDirection,
        string $markdownFileName,
    ): bool {
        $dot = self::getDiagramCode(DiagramSyntax::Dot, $databaseData, $diagramDirection);
        $dotFile = dirname($markdownFileName).'/'.self::GRAPHVIZ_BASENAME.'.dot';
        $pngFile = dirname($markdownFileName).'/'.self::GRAPHVIZ_BASENAME.'.png';
        // write the dot file

        File::put($dotFile, $dot);

        // call the graphviz command to convert the dot file into png
        $process = Process::run("dot -Tpng {$dotFile} -o {$pngFile}");

        return $process->successful();
    }

    /**
     * @param  array<string, array<string|null, EloquentRelation>>  $databaseData
     */
    public static function getDiagramCode(DiagramSyntax $style, array $databaseData, DiagramDirection $diagramDirection): string
    {
        $return = $style->start($diagramDirection);
        $links = [];
        $polyTables = [];

        foreach ($databaseData as $table1 => $data) {
            foreach ($data as $table2 => $relation) {
                if ($table2) {
                    $links[] = [$table1, $table2, $relation];
                }
                if ($style === DiagramSyntax::Mermaid) {
                    if ($relation === EloquentRelation::morphTo) {
                        $polyTables[$table1] = true;
                    } elseif ($relation->isMorph()) {
                        if ($table2) {
                            $polyTables[$table2] = true;
                        }
                    }
                }
            }
        }

        if ($style === DiagramSyntax::Mermaid) {
            foreach (array_keys($polyTables) as $table) {
                $return .= "    $table($table)\n";
            }
            $return .= "    classDef poly fill:#f9f,stroke:#333,stroke-width:2px\n";
            foreach (array_keys($polyTables) as $table) {
                $return .= "    class $table poly\n";
            }
        }

        foreach ($links as $link) {
            [$table1, $table2, $relation] = $link;
            if ($relation->isMorph()) {
                $return .= $style->morph($table1, $table2);
            } elseif ($relation->isDoubleLine()) {
                $return .= $style->double($table1, $table2);
            } else {
                $return .= $style->normal($table1, $table2);
            }
        }

        if ($style === DiagramSyntax::Mermaid) {
            foreach ($links as $index => $link) {
                $relation = $link[2];
                if ($relation->isMorph()) {
                    $return .= "    linkStyle $index stroke:#3498db,stroke-width:2px\n";
                } elseif ($relation->isDoubleLine()) {
                    $return .= "    linkStyle $index stroke:#e67e22,stroke-width:4px\n";
                } else {
                    $return .= "    linkStyle $index stroke:#2ecc71,stroke-width:3px\n";
                }
            }
            // Add legend as a subgraph
            $return .= "\n    subgraph Legend\n".
                "        direction TB\n".
                "        L1(Polymorphic) -.-> L2[Target]\n".
                "        L3[One-Way] -.-> L4[Target]\n".
                "        L5[Two-Way] <==> L6[Target]\n".
                "    end\n".
                "    class L1 poly\n";
            $idx = count($links);
            $return .= "    linkStyle $idx stroke:#3498db,stroke-width:2px\n";
            $idx++;
            $return .= "    linkStyle $idx stroke:#2ecc71,stroke-width:3px\n";
            $idx++;
            $return .= "    linkStyle $idx stroke:#e67e22,stroke-width:4px\n";
        }

        $return .= $style->end()."\n";

        return $return;
    }
}
