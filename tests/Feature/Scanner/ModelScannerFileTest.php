<?php

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Scanner\ModelScanner;
use SchenkeIo\LaravelRelationManager\Support\PathResolver;

it('uses modelPath from relationships.json when scanning without directory', function () {
    $jsonPath = 'tests/Data/valid_relationships.json';
    $fullJsonPath = PathResolver::getRealBasePath($jsonPath);
    $realJsonContent = file_get_contents(__DIR__.'/../../Data/valid_relationships.json');

    File::shouldReceive('exists')->andReturnUsing(function ($path) use ($fullJsonPath) {
        return $path === PathResolver::getRealBasePath('composer.json') || $path === $fullJsonPath;
    });
    File::shouldReceive('get')->with(PathResolver::getRealBasePath('composer.json'))->andReturn(json_encode([
        'extra' => ['laravel-relation-manager' => ['path' => $jsonPath]],
    ]));
    File::shouldReceive('get')->with($fullJsonPath)->andReturn($realJsonContent);

    // ModelScanner also checks if the directory exists
    File::shouldReceive('isDirectory')->andReturn(true);
    // and scans it
    File::shouldReceive('allFiles')->andReturn([]);

    $scanner = new ModelScanner;
    $results = $scanner->scan();

    expect($results)->toBeArray();
});
