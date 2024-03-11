<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;
use Workbench\App\Console\Commands\RunTestProjectManagerCommand;
use Workbench\App\Console\Commands\WriteMainFilesCommand;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // overwrite the default
        $config = $this->app->make('config');
        $config->set('relation-manager', require __DIR__.'/../../config/relation-manager.php');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->commands([
            RunTestProjectManagerCommand::class,
            WriteMainFilesCommand::class,
        ]);
    }
}
