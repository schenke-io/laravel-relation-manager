<?php

namespace SchenkeIo\LaravelRelationManager\Writer;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use SchenkeIo\LaravelRelationManager\Enums\DiagramDirection;
use SchenkeIo\LaravelRelationManager\Enums\DiagramSyntax;
use SchenkeIo\LaravelRelationManager\Enums\Relation;

/**
 * Service to generate diagram code (Mermaid, Graphviz) from relationship data.
 */
readonly class GetDiagram
{
    public const GRAPHVIZ_BASENAME = 'diagram';

    /**
     * @param  array<string, array<string|null, Relation>>  $databaseData
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
     * @param  array<string, array<string|null, Relation>>  $databaseData
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
     * @param  array<string, array<string|null, Relation>>  $databaseData
     */
    public static function getDiagramCode(DiagramSyntax $style, array $databaseData, DiagramDirection $diagramDirection): string
    {
        $return = $style->start($diagramDirection);

        foreach ($databaseData as $table1 => $data) {
            foreach ($data as $table2 => $relation) {
                if ($table2) {
                    if ($relation->isMorph()) {
                        $return .= $style->morph($table1, $table2);
                    } elseif ($relation->isDoubleLine()) {
                        $return .= $style->double($table1, $table2);
                    } else {
                        $return .= $style->normal($table1, $table2);
                    }
                }
            }
        }
        $return .= $style->end()."\n";

        return $return;
    }
}
