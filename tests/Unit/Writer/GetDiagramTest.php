<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use SchenkeIo\LaravelRelationManager\Enums\DiagramDirection;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;
use SchenkeIo\LaravelRelationManager\Writer\GetDiagram;

test('getGraphvizCode returns an image tag', function () {
    expect(GetDiagram::getGraphvizCode())->toContain("<img src='diagram.png'");
});

test('writeGraphvizFile writes file and calls dot command and returns success', function () {
    File::shouldReceive('put')->once();
    Process::fake([
        'dot *' => Process::result('ok', exitCode: 0),
    ]);

    $result = GetDiagram::writeGraphvizFile([], DiagramDirection::LR, 'test.md');

    expect($result)->toBeTrue();
    Process::assertRan(function ($process) {
        return str_contains($process->command, 'dot -Tpng');
    });
});

test('writeGraphvizFile returns false if dot command fails', function () {
    File::shouldReceive('put')->once();
    Process::fake([
        'dot *' => Process::result('error', exitCode: 1),
    ]);

    $result = GetDiagram::writeGraphvizFile([], DiagramDirection::LR, 'test.md');

    expect($result)->toBeFalse();
});

test('getDiagramCode handles morph and double relations', function () {
    $data = [
        'table1' => [
            'table2' => EloquentRelation::morphOne,
            'table3' => EloquentRelation::belongsToMany,
            'table4' => EloquentRelation::hasOne,
        ],
    ];

    $mermaid = GetDiagram::getMermaidCode($data, DiagramDirection::LR);

    expect($mermaid)->toContain('table1 -.-> table2')  // morph
        ->and($mermaid)->toContain('table1 <==> table3') // double
        ->and($mermaid)->toContain('table1 -.-> table4') // normal
        ->and($mermaid)->toContain('table2(table2)') // poly node
        ->and($mermaid)->toContain('classDef poly')
        ->and($mermaid)->toContain('class table2 poly');
});
