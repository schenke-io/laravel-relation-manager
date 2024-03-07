<?php

namespace SchenkeIo\LaravelRelationManager;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use SchenkeIo\LaravelRelationManager\Define\RelationsHandler;

class PackageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RelationsHandler::class, function ($app) {
            return new RelationsHandler(new Filesystem);
        });
    }
}
