<?php

use SchenkeIo\LaravelRelationManager\Writer\GetTable;

test('it can generate html table', function () {
    $table = [
        ['Header 1', 'Header 2'],
        [
            ['Row 1 Col 1', 'Row 1 Col 2'],
            ['Row 2 Col 1', 'Row 2 Col 2'],
        ],
    ];
    $html = GetTable::getHtml($table);
    expect($html)->toContain('<table>')
        ->and($html)->toContain('<th>Header 1</th>')
        ->and($html)->toContain('<td>Row 1 Col 1</td>');
});

test('it can generate markdown table', function () {
    $table = [
        ['Header 1', 'Header 2'],
        [
            ['Row 1 Col 1', 'Row 1 Col 2'],
        ],
    ];
    $markdown = GetTable::getMarkdown($table);
    expect($markdown)->toContain('| Header 1 | Header 2 |')
        ->and($markdown)->toContain('| --- | --- |')
        ->and($markdown)->toContain('| Row 1 Col 1 | Row 1 Col 2 |');
});
