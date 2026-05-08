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
    /**
     * Configure the package with its name and registered commands.
     */
    public function configurePackage(Package $package): void
    {
        $package->name('laravel-relation-manager')
            ->hasCommands([
                Console\RelationExtractCommand::class,
                Console\RelationVerifyCommand::class,
                Console\RelationDrawCommand::class,
            ]);
    }

    /**
     * Register singleton services after the package is registered.
     */
    public function packageRegistered(): void
    {
        $this->app->singleton(ModelScanner::class, function () {
            return new ModelScanner;
        });
    }
}
