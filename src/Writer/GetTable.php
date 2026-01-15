<?php

namespace SchenkeIo\LaravelRelationManager\Writer;

/**
 * Utility class to generate HTML or Markdown tables from data arrays.
 */
final readonly class GetTable
{
    /**
     * @param  array{0: list<string>, 1: list<list<string>>}  $table
     */
    public static function getHtml(array $table): string
    {
        [$headers, $tableRows] = $table;
        $html = '<tr><th>'.implode('</th><th>', $headers).'</th></tr>';
        foreach ($tableRows as $row) {
            $html .= '<tr><td>'.implode('</td><td>', $row)."</td></tr>\n";
        }

        return "<table>\n$html\n</table>\n";
    }

    /**
     * @param  array{0: list<string>, 1: list<list<string>>}  $table
     */
    public static function getMarkdown(array $table): string
    {
        [$headers, $tableRows] = $table;
        $markdown = '| '.implode(' | ', $headers)." |\n";
        $markdown .= '| '.implode(' | ', array_fill(0, count($headers), '---'))." |\n";
        foreach ($tableRows as $row) {
            $markdown .= '| '.implode(' | ', $row)." |\n";
        }

        return $markdown;
    }
}
