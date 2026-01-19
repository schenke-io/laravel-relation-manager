<?php

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Facades\ModelScanner;
use SchenkeIo\LaravelRelationManager\Support\PathResolver;

beforeEach(function () {
    File::shouldReceive('isFile')->andReturn(true)->byDefault();
    File::shouldReceive('isDirectory')->andReturn(false)->byDefault();
});

it('can extract relationships to json', function () {
    ModelScanner::shouldReceive('scan')
        ->once()
        ->andReturn(['Model' => ['relation' => ['type' => \SchenkeIo\LaravelRelationManager\Enums\EloquentRelation::hasOne, 'related' => 'RelatedModel']]]);

    File::shouldReceive('exists')->andReturn(false);

    File::shouldReceive('put')
        ->once()
        ->with(PathResolver::getRealBasePath('.relationships.json'), Mockery::any())
        ->andReturn(true);

    $this->artisan('relation:extract')
        ->expectsOutput('Scanning models...')
        ->expectsOutputToContain('Relationships exported to')
        ->assertExitCode(0);
});

it('fails when export fails', function () {
    ModelScanner::shouldReceive('scan')
        ->once()
        ->andReturn([]);

    File::shouldReceive('exists')->andReturn(false);

    File::shouldReceive('put')
        ->once()
        ->andReturn(false);

    $this->artisan('relation:extract')
        ->expectsOutput('Scanning models...')
        ->expectsOutputToContain('Failed to export relationships to')
        ->assertExitCode(1);
});

it('fails if scanner throws exception', function () {
    File::shouldReceive('exists')->andReturn(false);

    ModelScanner::shouldReceive('scan')
        ->once()
        ->andThrow(new \Exception('Scanner error'));

    $this->artisan('relation:extract')
        ->expectsOutput('Scanning models...')
        ->expectsOutput('Scanner error')
        ->assertExitCode(1);
});
