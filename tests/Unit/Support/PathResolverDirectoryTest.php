<?php

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Support\PathResolver;

uses()->group('support');

it('appends default filename if composer.json path is a directory', function () {
    $base = PathResolver::getRealBasePath();
    $composerPath = $base.DIRECTORY_SEPARATOR.'composer.json';
    $customDir = 'custom/dir';
    $customDirPath = $base.DIRECTORY_SEPARATOR.$customDir;

    File::shouldReceive('exists')->with($base.DIRECTORY_SEPARATOR.'.relationships.json')->andReturn(false);
    File::shouldReceive('exists')->with($composerPath)->andReturn(true);
    File::shouldReceive('get')->with($composerPath)->andReturn(json_encode([
        'extra' => [
            'laravel-relation-manager' => [
                'path' => $customDir,
            ],
        ],
    ]));

    File::shouldReceive('isDirectory')->with($customDirPath)->andReturn(true);

    expect(PathResolver::getRelationshipFilePath())->toBe($customDirPath.DIRECTORY_SEPARATOR.PathResolver::DEFAULT_FILENAME);
});
