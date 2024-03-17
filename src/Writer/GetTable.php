<?php

namespace SchenkeIo\LaravelRelationManager\Writer;

final class GetTable
{
    public function __construct(public array $header)
    {
    }

    public static function header(array $header): self
    {
        return new self($header);
    }

    public function getHtml(array $tableRows): string
    {
        $html = '';
        foreach ($tableRows as $row) {
            $html .= '<tr><td>'.implode('</td><td>', $row)."</td></tr>\n";
        }

        return <<<HTML
<table>
<tr><th>model</th><th>direct related</th><th>indirect related</th></tr>
$html
</table>
HTML;
    }
}
