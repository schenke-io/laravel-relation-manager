<?php

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Facades\ModelScanner;

it('can extract relationships to json', function () {
    ModelScanner::shouldReceive('scan')
        ->once()
        ->andReturn(['Model' => ['relation' => ['type' => \SchenkeIo\LaravelRelationManager\Enums\Relation::hasOne, 'related' => 'RelatedModel']]]);

    File::shouldReceive('exists')->andReturn(false);

    File::shouldReceive('put')
        ->once()
        ->with(base_path('.relationships.json'), Mockery::any())
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
    ModelScanner::shouldReceive('scan')
        ->once()
        ->andThrow(new \Exception('Scanner error'));

    $this->artisan('relation:extract')
        ->expectsOutput('Scanning models...')
        ->expectsOutput('Scanner error')
        ->assertExitCode(1);
});
