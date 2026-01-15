<?php

namespace SchenkeIo\LaravelRelationManager\Writer;

/**
 * Utility class to generate HTML or Markdown tables from data arrays.
 */
final readonly class GetTable
{
    /**
     * @param  array{0: list<string>, 1: list<list<string>>}  $table
     * @param  int[]  $rowspanColumns
     */
    public static function getHtml(array $table, array $rowspanColumns = []): string
    {
        [$headers, $tableRows] = $table;
        $html = '<tr><th>'.implode('</th><th>', $headers).'</th></tr>';

        $rowSpanTracker = []; // colIndex => [value, count, startRowIndex]
        $rowsToOutput = []; // rowIndex => [colIndex => [value, rowspan]]

        foreach ($tableRows as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                if (in_array($colIndex, $rowspanColumns)) {
                    if (isset($rowSpanTracker[$colIndex]) && $rowSpanTracker[$colIndex]['value'] === $value) {
                        $rowSpanTracker[$colIndex]['count']++;
                        // Don't output this cell in this row
                        $rowsToOutput[$rowIndex][$colIndex] = null;
                    } else {
                        // New value or first row
                        if (isset($rowSpanTracker[$colIndex])) {
                            // Update the rowspan of the starting cell
                            $rowsToOutput[$rowSpanTracker[$colIndex]['startRowIndex']][$colIndex]['rowspan'] = $rowSpanTracker[$colIndex]['count'];
                        }
                        $rowSpanTracker[$colIndex] = [
                            'value' => $value,
                            'count' => 1,
                            'startRowIndex' => $rowIndex,
                        ];
                        $rowsToOutput[$rowIndex][$colIndex] = ['value' => $value, 'rowspan' => 1];
                    }
                } else {
                    $rowsToOutput[$rowIndex][$colIndex] = ['value' => $value, 'rowspan' => 1];
                }
            }
        }
        // Final update for last groups
        foreach ($rowSpanTracker as $colIndex => $tracker) {
            $rowsToOutput[$tracker['startRowIndex']][$colIndex]['rowspan'] = $tracker['count'];
        }

        foreach ($rowsToOutput as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                if ($cell === null) {
                    continue;
                }
                $rowspanAttr = $cell['rowspan'] > 1 ? ' rowspan="'.$cell['rowspan'].'"' : '';
                $html .= '<td'.$rowspanAttr.'>'.$cell['value'].'</td>';
            }
            $html .= "</tr>\n";
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
