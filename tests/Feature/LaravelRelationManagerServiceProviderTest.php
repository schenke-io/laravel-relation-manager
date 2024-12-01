<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature;

use Mockery;
use SchenkeIo\LaravelRelationManager\LaravelRelationManagerServiceProvider;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use Spatie\LaravelPackageTools\Exceptions\InvalidPackage;
use Spatie\LaravelPackageTools\Package;

class LaravelRelationManagerServiceProviderTest extends TestCase
{
    /**
     * @throws InvalidPackage
     */
    public function test_configure_package()
    {
        $mockPackage = Mockery::mock(Package::class);
        $mockPackage->shouldReceive('name')->once();
        $mockPackage->shouldReceive('hasConfigFile')->once();
        $mockPackage->shouldReceive('hasInstallCommand')->once();

        $provider = new LaravelRelationManagerServiceProvider($this->app);
        $provider->register();
        $provider->configurePackage($mockPackage);
    }
}
