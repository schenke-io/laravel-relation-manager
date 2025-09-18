<?php

namespace SchenkeIo\LaravelRelationManager\Writer;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Process;
use SchenkeIo\LaravelRelationManager\Enums\DiagramDirection;
use SchenkeIo\LaravelRelationManager\Enums\DiagramSyntax;
use SchenkeIo\LaravelRelationManager\Enums\Relation;

class GetDiagramm
{
    public const GRAPHVIZ_BASENAME = 'diagram';

    /**
     * @param  array<string, array<string|null, Relation>>  $databaseData
     */
    public static function getMermaidCode(array $databaseData, DiagramDirection $diagrammDirection): string
    {
        return self::getDiagrammCode(DiagramSyntax::Mermaid, $databaseData, $diagrammDirection);
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
        DiagramDirection $diagrammDirection,
        string $markdownFileName,
        Filesystem $fileSystem = new Filesystem
    ): void {
        $dot = self::getDiagrammCode(DiagramSyntax::Dot, $databaseData, $diagrammDirection);
        $dotFile = dirname($markdownFileName).'/'.self::GRAPHVIZ_BASENAME.'.dot';
        $pngFile = dirname($markdownFileName).'/'.self::GRAPHVIZ_BASENAME.'.png';
        // write the dot file

        $fileSystem->put($dotFile, $dot);

        // call the graphviz command to convert the dot file into png
        Process::run("dot -Tpng {$dotFile} -o {$pngFile}");
    }

    /**
     * @param  array<string, array<string|null, Relation>>  $databaseData
     */
    public static function getDiagrammCode(DiagramSyntax $style, array $databaseData, DiagramDirection $diagrammDirection): string
    {
        $return = $style->start($diagrammDirection);

        foreach ($databaseData as $table1 => $data) {
            /** @var Relation $relation */
            foreach ($data as $table2 => $relation) {
                if ($table2) {
                    if ($relation->isMorph()) {
                        $return .= $style->morph($table1, $table2);
                    } elseif ($relation->isDouble()) {
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
