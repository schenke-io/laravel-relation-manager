<?php

namespace SchenkeIo\LaravelRelationManager;

use SchenkeIo\LaravelRelationManager\Scanner\ModelScanner;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * Service provider for the Laravel Relation Manager package,
 * responsible for registering commands and singleton services.
 */
class LaravelRelationManagerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('laravel-relation-manager')
            ->hasCommands([
                Console\RelationExtractCommand::class,
                Console\RelationVerifyCommand::class,
                Console\RelationDrawCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ModelScanner::class, function () {
            return new ModelScanner;
        });
    }
}
