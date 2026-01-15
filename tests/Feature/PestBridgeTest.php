<?php

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Facades\ModelScanner;
use SchenkeIo\LaravelRelationManager\Pest\RelationTestBridge;

/*
 * We use a relative path that will be passed to base_path()
 * but we don't call base_path() here.
 */
RelationTestBridge::all('dummy_relationships.json');

beforeEach(function () {
    $path = base_path('dummy_relationships.json');

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
