<?php

use Illuminate\Support\Facades\File;

it('fails if .relationships.json is missing', function () {
    File::shouldReceive('exists')->andReturn(false);

    $this->artisan('relation:draw')
        ->expectsOutput('.relationships.json not found. Run relation:extract first.')
        ->assertExitCode(1);
});

it('can draw to specified filename', function () {
    File::shouldReceive('exists')->andReturn(true);
    File::shouldReceive('get')->andReturn(json_encode([
        'models' => [
            'App\Models\User' => [
                'posts' => ['type' => 'hasMany', 'related' => 'App\Models\Post'],
            ],
        ],
    ]));

    File::shouldReceive('put')
        ->once()
        ->with('test.md', Mockery::any())
        ->andReturn(true);

    $this->artisan('relation:draw test.md')
        ->expectsOutput('Markdown written to test.md')
        ->assertExitCode(0);
});

it('can draw to filename from config', function () {
    File::shouldReceive('exists')->andReturn(true);
    File::shouldReceive('get')->andReturn(json_encode([
        'config' => ['markdown_path' => 'config_path.md'],
        'models' => [],
    ]));

    File::shouldReceive('put')
        ->once()
        ->with('config_path.md', Mockery::any())
        ->andReturn(true);

    $this->artisan('relation:draw')
        ->expectsOutput('Markdown written to config_path.md')
        ->assertExitCode(0);
});

it('fails if file write fails', function () {
    File::shouldReceive('exists')->andReturn(true);
    File::shouldReceive('get')->andReturn(json_encode([
        'models' => [],
    ]));

    File::shouldReceive('put')->andReturn(false);

    $this->artisan('relation:draw test.md')
        ->expectsOutput('Failed to write to test.md')
        ->assertExitCode(1);
});

it('shows warnings when graphviz generation fails', function () {
    File::shouldReceive('exists')->andReturn(true);
    File::shouldReceive('get')->andReturn(json_encode([
        'config' => ['use_mermaid' => false],
        'models' => [],
    ]));

    \Illuminate\Support\Facades\Process::fake([
        'dot *' => \Illuminate\Support\Facades\Process::result('error', exitCode: 1),
    ]);

    File::shouldReceive('put')->andReturn(true);

    $this->artisan('relation:draw test.md')
        ->expectsOutput('Markdown written to test.md')
        ->expectsOutput('Graphviz generation failed. Please ensure \'dot\' is installed and in your PATH.')
        ->assertExitCode(0);
});
