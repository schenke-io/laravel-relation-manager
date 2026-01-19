<?php

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Support\PathResolver;

it('getModelPath finds relative model path from relationships.json', function () {
    $base = PathResolver::getRealBasePath();
    $relFile = $base.DIRECTORY_SEPARATOR.'.relationships.json';
    $modelPath = 'custom/models';
    $absModelPath = $base.DIRECTORY_SEPARATOR.$modelPath;

    File::shouldReceive('exists')->with($relFile)->andReturn(true);
    File::shouldReceive('get')->with($relFile)->andReturn(json_encode([
        'config' => ['modelPath' => $modelPath],
    ]));

    // First call to File::isDirectory($modelPath) -> false
    // Second call to File::isDirectory($absModelPath) -> true
    File::shouldReceive('isDirectory')->with($modelPath)->andReturn(false);
    File::shouldReceive('isDirectory')->with($absModelPath)->andReturn(true);

    expect(PathResolver::getModelPath())->toBe($absModelPath);
});

it('getModelPath finds absolute model path from relationships.json', function () {
    $base = PathResolver::getRealBasePath();
    $relFile = $base.DIRECTORY_SEPARATOR.'.relationships.json';
    $modelPath = '/abs/path/to/models';

    File::shouldReceive('exists')->with($relFile)->andReturn(true);
    File::shouldReceive('get')->with($relFile)->andReturn(json_encode([
        'config' => ['modelPath' => $modelPath],
    ]));

    File::shouldReceive('isDirectory')->with($modelPath)->andReturn(true);

    expect(PathResolver::getModelPath())->toBe($modelPath);
});

it('getModelPath handles missing composer.json', function () {
    $base = PathResolver::getRealBasePath();
    $relFile = $base.DIRECTORY_SEPARATOR.'.relationships.json';
    $composerFile = $base.DIRECTORY_SEPARATOR.'composer.json';

    File::shouldReceive('exists')->with($relFile)->andReturn(false);
    File::shouldReceive('exists')->with($composerFile)->andReturn(false);

    // It will then try auth model
    config(['auth.providers.users.model' => null]);

    // Then isApp
    File::shouldReceive('exists')->with($base.DIRECTORY_SEPARATOR.'app')->andReturn(false);

    // Then isPackage
    File::shouldReceive('exists')->with($base.DIRECTORY_SEPARATOR.'src')->andReturn(false);

    // Final fallbacks
    File::shouldReceive('exists')->with($base.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Models')->andReturn(false);

    expect(PathResolver::getModelPath())->toContain('src'.DIRECTORY_SEPARATOR.'Models');
});

it('getRelationshipFilePath handles composer.json without extra config', function () {
    $base = PathResolver::getRealBasePath();
    $relFile = $base.DIRECTORY_SEPARATOR.'.relationships.json';
    $composerFile = $base.DIRECTORY_SEPARATOR.'composer.json';

    File::shouldReceive('exists')->with($relFile)->andReturn(false);
    File::shouldReceive('exists')->with($composerFile)->andReturn(true);
    File::shouldReceive('get')->with($composerFile)->andReturn(json_encode([]));

    // Then isPackage
    File::shouldReceive('exists')->with($base.DIRECTORY_SEPARATOR.'src')->andReturn(false);

    expect(PathResolver::getRelationshipFilePath())->toBe($relFile);
});

it('getModelPath finds path via auth model', function () {
    $base = PathResolver::getRealBasePath();
    File::shouldReceive('exists')->andReturn(false);

    // Mock auth model to a real class
    config(['auth.providers.users.model' => \SchenkeIo\LaravelRelationManager\Support\PathResolver::class]);

    $path = PathResolver::getModelPath();
    expect($path)->toBe(dirname((new ReflectionClass(PathResolver::class))->getFileName()));
});
