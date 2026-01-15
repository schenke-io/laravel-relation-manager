<?php

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Support\PathResolver;

uses()->group('support');

it('returns path from ENV if present', function () {
    $envPath = '/custom/env/path.json';
    $_ENV['LARAVEL_RELATIONSHIPS_JSON'] = $envPath;
    expect(PathResolver::getRelationshipFilePath())->toBe($envPath);
    unset($_ENV['LARAVEL_RELATIONSHIPS_JSON']);
});

it('returns path from composer.json if present', function () {
    File::shouldReceive('exists')->andReturnUsing(fn ($path) => $path === base_path('composer.json'));
    File::shouldReceive('get')
        ->with(base_path('composer.json'))
        ->andReturn(json_encode([
            'extra' => [
                'laravel-relation-manager' => [
                    'path' => 'custom/path.json',
                ],
            ],
        ]));

    expect(PathResolver::getRelationshipFilePath())->toBe(base_path('custom/path.json'));
});

it('returns workbench path if composer.json has no path but workbench file exists', function () {
    File::shouldReceive('exists')->andReturnUsing(fn ($path) => $path === base_path('composer.json') ||
        $path === base_path('workbench/.relationships.json')
    );
    File::shouldReceive('get')
        ->with(base_path('composer.json'))
        ->andReturn(json_encode([]));

    expect(PathResolver::getRelationshipFilePath())->toBe(base_path('workbench/.relationships.json'));
});

it('returns default path if no other options are found', function () {
    File::shouldReceive('exists')->andReturn(false);

    expect(PathResolver::getRelationshipFilePath())->toBe(base_path('.relationships.json'));
});
