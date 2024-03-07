<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;
use Workbench\App\Console\Commands\RunTestProjectCommand;
use Workbench\App\Console\Commands\WriteMainFilesCommand;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->commands([
            RunTestProjectCommand::class,
            WriteMainFilesCommand::class,
        ]);
    }
}
