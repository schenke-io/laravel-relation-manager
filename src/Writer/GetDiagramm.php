<?php

namespace SchenkeIo\LaravelRelationManager\Writer;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Process;
use SchenkeIo\LaravelRelationManager\Define\RelationsEnum;

class GetDiagramm
{
    public const GRAPHVIZ_BASENAME = 'diagram';

    public static function getMermaidCode(array $databaseData, DiagramDirection $diagrammDirection): string
    {
        return self::getDiagrammCode(DigramSyntax::Mermaid, $databaseData, $diagrammDirection);
    }

    public static function getGraphvizCode(): string
    {
        return "<img src='".self::GRAPHVIZ_BASENAME.".png' alt='".self::GRAPHVIZ_BASENAME."' />";
    }

    public static function writeGraphvizFile(
        array $databaseData,
        DiagramDirection $diagrammDirection,
        string $markdownFileName,
        Filesystem $fileSystem = new Filesystem
    ): void {
        $dot = self::getDiagrammCode(DigramSyntax::Dot, $databaseData, $diagrammDirection);
        $dotFile = dirname($markdownFileName).'/'.self::GRAPHVIZ_BASENAME.'.dot';
        $pngFile = dirname($markdownFileName).'/'.self::GRAPHVIZ_BASENAME.'.png';
        // write the dot file

        $fileSystem->put($dotFile, $dot);

        // call the graphviz command to convert the dot file into png
        Process::run("dot -Tpng {$dotFile} -o {$pngFile}");
    }

    public static function getDiagrammCode(DigramSyntax $style, array $databaseData, DiagramDirection $diagrammDirection): string
    {
        $return = $style->start($diagrammDirection);

        foreach ($databaseData as $table1 => $data) {
            /** @var RelationsEnum $relation */
            foreach ($data as $table2 => $relation) {
                if ($table2) {
                    if ($relation == RelationsEnum::castEnum) {
                        $return .= $style->enum($table1, $table2);
                    } elseif ($relation->isMorph()) {
                        $return .= $style->morph($table1, $table2);
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
