<?php

namespace SchenkeIo\LaravelRelationManager\Writer;

final class GetTable
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
}
