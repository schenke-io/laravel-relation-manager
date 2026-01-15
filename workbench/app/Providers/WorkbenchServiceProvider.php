<?php

namespace Workbench\App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void {}

    /**
     * Bootstrap services.
     */
    public function boot(): void {}

    public function packageBooted(): void
    {
        if ($this->app->runningInConsole()) {
            $argv = $_SERVER['argv'] ?? [];
            $commandString = implode(' ', $argv);

            if (str_contains($commandString, 'workbench:build') || str_contains($commandString, 'build')) {
                $dbPath = $this->app->databasePath('database.sqlite');
                if (! file_exists($dbPath)) {
                    if (! is_dir(dirname($dbPath))) {
                        mkdir(dirname($dbPath), 0755, true);
                    }
                    touch($dbPath);
                }
                Artisan::call('migrate:fresh', [
                    '--seed' => true,
                    '--seeder' => 'Workbench\Database\Seeders\DatabaseSeeder',
                ]);
            }
        }
    }
}
