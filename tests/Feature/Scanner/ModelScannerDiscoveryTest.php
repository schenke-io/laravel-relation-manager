<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Scanner;

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Scanner\ModelScanner;
use SchenkeIo\LaravelRelationManager\Tests\Models\User;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class ModelScannerDiscoveryTest extends TestCase
{
    public function test_scan_finds_directory_via_base_path()
    {
        $jsonPath = 'relative/path';

        File::shouldReceive('exists')->andReturn(true);
        File::shouldReceive('get')->andReturn(json_encode([
            'config' => ['modelPath' => $jsonPath],
        ]));

        File::shouldReceive('isDirectory')->with($jsonPath)->andReturn(false);
        File::shouldReceive('isDirectory')->with(base_path($jsonPath))->andReturn(true);
        File::shouldReceive('allFiles')->andReturn([]);

        $scanner = new ModelScanner;
        $results = $scanner->scan();
        $this->assertIsArray($results);
    }

    public function test_scan_finds_directory_via_auth_model()
    {
        // Ensure no relationship file is found
        File::shouldReceive('exists')->andReturn(false);

        config(['auth.providers.users.model' => User::class]);

        File::shouldReceive('isDirectory')->andReturn(true);
        File::shouldReceive('allFiles')->andReturn([]);

        $scanner = new ModelScanner;
        $results = $scanner->scan();
        $this->assertIsArray($results);
    }
}
