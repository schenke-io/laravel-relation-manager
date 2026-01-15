<?php

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;
use SchenkeIo\LaravelRelationManager\Facades\ModelScanner;

it('passes when relationships are in sync', function () {
    ModelScanner::shouldReceive('scan')->once()->andReturn([]);
    ModelScanner::shouldReceive('getDatabaseColumns')->once()->andReturn([]);

    File::shouldReceive('exists')->andReturn(true);
    File::shouldReceive('get')->andReturn(json_encode([
        'config' => [],
        'models' => [],
    ]));

    $this->artisan('relation:verify')
        ->expectsOutput('Verifying relationships...')
        ->expectsOutput('Relationships are in sync and no logic warnings found.')
        ->assertExitCode(0);
});

it('fails and shows errors when relationships are out of sync', function () {
    ModelScanner::shouldReceive('scan')->once()->andReturn([
        'App\Models\User' => [],
    ]);
    ModelScanner::shouldReceive('getDatabaseColumns')->once()->andReturn([]);

    File::shouldReceive('exists')->andReturn(true);
    File::shouldReceive('get')->andReturn(json_encode([
        'config' => [],
        'models' => [
            'App\Models\Post' => [],
        ],
    ]));

    $this->artisan('relation:verify')
        ->expectsOutput('Verifying relationships...')
        ->expectsOutputToContain('Model App\Models\Post missing in implementation')
        ->assertExitCode(1);
});

it('fails if .relationships.json is missing', function () {
    ModelScanner::shouldReceive('scan')->andReturn([]);
    File::shouldReceive('exists')->andReturn(false);

    $this->artisan('relation:verify')
        ->expectsOutput('Verifying relationships...')
        ->expectsOutputToContain('Failed to load or parse')
        ->assertExitCode(1);
});

it('fails if scanner fails', function () {
    File::shouldReceive('exists')->andReturn(true);
    File::shouldReceive('get')->andReturn(json_encode(['models' => []]));

    ModelScanner::shouldReceive('scan')->andThrow(new \Exception('Scan failed'));

    $this->artisan('relation:verify')
        ->expectsOutput('Verifying relationships...')
        ->expectsOutput('Scan failed')
        ->assertExitCode(1);
});

it('shows warnings when relationships are in sync but have logic issues', function () {
    ModelScanner::shouldReceive('scan')->once()->andReturn([
        'App\Models\User' => [
            'groups' => ['type' => EloquentRelation::belongsToMany, 'related' => 'App\Models\Group'],
        ],
        'App\Models\Group' => [],
    ]);
    ModelScanner::shouldReceive('getDatabaseColumns')->once()->andReturn([]);

    File::shouldReceive('exists')->andReturn(true);
    File::shouldReceive('get')->andReturn(json_encode([
        'models' => [
            'App\Models\User' => [
                'methods' => [
                    'groups' => ['type' => 'belongsToMany', 'related' => 'App\Models\Group'],
                ],
            ],
            'App\Models\Group' => ['methods' => []],
        ],
    ]));

    $this->artisan('relation:verify')
        ->expectsOutput('Verifying relationships...')
        ->expectsOutput('Relationships are in sync, but logic warnings were found.')
        ->assertExitCode(0);
});
