<?php

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Facades\ModelScanner;
use SchenkeIo\LaravelRelationManager\Pest\RelationTestBridge;
use SchenkeIo\LaravelRelationManager\Support\PathResolver;

/*
 * We use a relative path that will be passed to getRealBasePath()
 * but we don't call it here.
 */
RelationTestBridge::all('dummy_relationships.json');

beforeEach(function () {
    $path = PathResolver::getRealBasePath('dummy_relationships.json');

    File::shouldReceive('isFile')->andReturn(true)->byDefault();
    File::shouldReceive('isDirectory')->andReturn(false)->byDefault();

    File::shouldReceive('exists')
        ->with($path)
        ->andReturn(true);

    File::shouldReceive('get')
        ->with($path)
        ->andReturn(json_encode([
            'config' => [
                'modelPath' => 'app/Models',
                'modelNamespace' => 'App\\Models\\',
            ],
            'models' => [],
        ]));

    ModelScanner::shouldReceive('scan')
        ->andReturn([]);
});
