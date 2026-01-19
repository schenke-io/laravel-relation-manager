<?php

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Exceptions\LaravelRelationManagerException;
use SchenkeIo\LaravelRelationManager\Scanner\ModelScanner;
use SchenkeIo\LaravelRelationManager\Support\PathResolver;

it('throws exception when directory not found', function () {
    File::shouldReceive('isDirectory')->andReturn(false);

    $scanner = new ModelScanner;
    $scanner->scan('non-existent-directory');
})->throws(LaravelRelationManagerException::class, 'Directory "non-existent-directory" not found');

it('scans app/Models by default', function () {
    File::shouldReceive('exists')->andReturn(false);
    File::shouldReceive('isDirectory')->andReturn(true);
    File::shouldReceive('allFiles')->andReturn([]);

    $scanner = new ModelScanner;
    expect($scanner->scan())->toBeArray();
});

it('scans app/Models when specified', function () {
    File::shouldReceive('exists')->andReturn(false);
    File::shouldReceive('isDirectory')->andReturn(true);
    File::shouldReceive('allFiles')->andReturn([]);

    $scanner = new ModelScanner;
    expect($scanner->scan('app/Models'))->toBeArray();
});

it('finds directory via real base path', function () {
    $dir = 'rel-dir';
    $realDir = PathResolver::getRealBasePath($dir);
    File::shouldReceive('isDirectory')->with($dir)->andReturn(false);
    File::shouldReceive('isDirectory')->with($realDir)->andReturn(true);
    File::shouldReceive('allFiles')->with($realDir)->andReturn([]);

    $scanner = new ModelScanner;
    expect($scanner->scan($dir))->toBeArray();
});
