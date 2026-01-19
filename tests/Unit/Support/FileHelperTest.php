<?php

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Support\FileHelper;

it('extracts class name from file', function () {
    File::shouldReceive('get')->andReturn('<?php namespace App\Models; class User {}');
    expect(FileHelper::getClassNameFromFile('path'))->toBe('App\Models\User');
});

it('returns null if namespace not found', function () {
    File::shouldReceive('get')->andReturn('<?php class User {}');
    expect(FileHelper::getClassNameFromFile('path'))->toBeNull();
});

it('returns null if class not found', function () {
    File::shouldReceive('get')->andReturn('<?php namespace App\Models;');
    expect(FileHelper::getClassNameFromFile('path'))->toBeNull();
});
