<?php

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Support\PathResolver;

uses()->group('support');

beforeEach(function () {
    config(['auth.providers.users.model' => 'NonExistentClass']);
    File::shouldReceive('isDirectory')->andReturn(false)->byDefault();
});

it('detects package mode correctly', function () {
    File::shouldReceive('exists')->with(PathResolver::getRealBasePath('src'))->andReturn(true);
    File::shouldReceive('exists')->with(PathResolver::getRealBasePath('workbench'))->andReturn(true);
    expect(PathResolver::isPackage())->toBeTrue();
});

it('detects app mode correctly', function () {
    File::shouldReceive('exists')->with(PathResolver::getRealBasePath('app'))->andReturn(true);
    File::shouldReceive('exists')->with(PathResolver::getRealBasePath('public'))->andReturn(true);
    expect(PathResolver::isApp())->toBeTrue();
});

it('returns path from composer.json if present', function () {
    File::shouldReceive('exists')->andReturnUsing(fn ($path) => $path === PathResolver::getRealBasePath('composer.json'));
    File::shouldReceive('isDirectory')->andReturn(false);
    File::shouldReceive('get')
        ->with(PathResolver::getRealBasePath('composer.json'))
        ->andReturn(json_encode([
            'extra' => [
                'laravel-relation-manager' => [
                    'path' => 'custom/path.json',
                ],
            ],
        ]));

    expect(PathResolver::getRelationshipFilePath())->toBe(PathResolver::getRealBasePath('custom/path.json'));
});

it('returns workbench path if in package and workbench file exists', function () {
    File::shouldReceive('exists')->andReturnUsing(fn ($path) => $path === PathResolver::getRealBasePath('src') ||
        $path === PathResolver::getRealBasePath('workbench') ||
        $path === PathResolver::getRealBasePath('workbench/.relationships.json')
    );
    File::shouldReceive('get')->andReturn(json_encode([]));

    expect(PathResolver::getRelationshipFilePath())->toBe(PathResolver::getRealBasePath('workbench/.relationships.json'));
});

it('returns default path if no other options are found', function () {
    File::shouldReceive('exists')->andReturn(false);

    expect(PathResolver::getRelationshipFilePath())->toBe(PathResolver::getRealBasePath('.relationships.json'));
});

it('can make a path relative', function () {
    $base = PathResolver::getRealBasePath();
    $path = $base.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Models';
    expect(PathResolver::makePathRelative($path))->toBe('app'.DIRECTORY_SEPARATOR.'Models');
});

it('leaves an already relative path as is', function () {
    expect(PathResolver::makePathRelative('app/Models'))->toBe('app/Models');
});

it('returns model path from composer.json if present', function () {
    File::shouldReceive('exists')->andReturnUsing(fn ($path) => $path === PathResolver::getRealBasePath('composer.json'));
    File::shouldReceive('get')
        ->with(PathResolver::getRealBasePath('composer.json'))
        ->andReturn(json_encode([
            'extra' => [
                'laravel-relation-manager' => [
                    'models' => 'src/Domain/Models',
                ],
            ],
        ]));

    expect(PathResolver::getModelPath())->toBe(PathResolver::getRealBasePath('src/Domain/Models'));
});

it('returns default model path for app', function () {
    File::shouldReceive('exists')->andReturnUsing(fn ($path) => $path === PathResolver::getRealBasePath('app') || $path === PathResolver::getRealBasePath('public')
    );
    File::shouldReceive('get')->andReturn(json_encode([]));

    expect(PathResolver::getModelPath())->toBe(PathResolver::getRealBasePath('app/Models'));
});

it('returns default model path for package', function () {
    File::shouldReceive('exists')->andReturnUsing(fn ($path) => $path === PathResolver::getRealBasePath('src') || $path === PathResolver::getRealBasePath('workbench')
    );
    File::shouldReceive('get')->andReturn(json_encode([]));

    expect(PathResolver::getModelPath())->toBe(PathResolver::getRealBasePath('src/Models'));
});

it('returns fallback app model path if neither app nor package but app/Models exists', function () {
    File::shouldReceive('exists')->andReturnUsing(fn ($path) => $path === PathResolver::getRealBasePath('app/Models')
    );
    File::shouldReceive('get')->andReturn(json_encode([]));
    File::shouldReceive('isDirectory')->andReturn(false);

    expect(PathResolver::getModelPath())->toBe(PathResolver::getRealBasePath('app/Models'));
});

it('returns fallback src model path if neither app nor package and app/Models missing', function () {
    File::shouldReceive('exists')->andReturn(false);
    File::shouldReceive('get')->andReturn(json_encode([]));
    File::shouldReceive('isDirectory')->andReturn(false);

    expect(PathResolver::getModelPath())->toBe(PathResolver::getRealBasePath('src/Models'));
});
