<?php

use SchenkeIo\LaravelRelationManager\Exceptions\LaravelRelationManagerException;

it('can create directory not found exception', function () {
    $e = LaravelRelationManagerException::directoryNotFound('dir');
    expect($e->getMessage())->toBe('Directory "dir" not found');
});

it('can create directory not writable exception', function () {
    $e = LaravelRelationManagerException::directoryNotWritable('dir');
    expect($e->getMessage())->toBe('Directory "dir" is not writable');
});

it('can create invalid class exception', function () {
    $e = LaravelRelationManagerException::invalidClass('class');
    expect($e->getMessage())->toBe('Class "class" is invalid');
});

it('can create laravel not loaded exception', function () {
    $e = LaravelRelationManagerException::laravelNotLoaded();
    expect($e->getMessage())->toBe('Laravel is not loaded');
});

it('can create model class not found exception', function () {
    $e = LaravelRelationManagerException::modelClassNotFound('model');
    expect($e->getMessage())->toBe('Model class "model" not found');
});
