<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Exceptions\LaravelRelationManagerException;

class ExceptionTest extends TestCase
{
    public function test_directory_not_found()
    {
        $e = LaravelRelationManagerException::directoryNotFound('/tmp');
        $this->assertEquals('Directory "/tmp" not found', $e->getMessage());
    }

    public function test_directory_not_writable()
    {
        $e = LaravelRelationManagerException::directoryNotWritable('/tmp');
        $this->assertEquals('Directory "/tmp" is not writable', $e->getMessage());
    }

    public function test_invalid_class()
    {
        $e = LaravelRelationManagerException::invalidClass('InvalidClass');
        $this->assertEquals('Class "InvalidClass" is invalid', $e->getMessage());
    }

    public function test_laravel_not_loaded()
    {
        $e = LaravelRelationManagerException::laravelNotLoaded();
        $this->assertEquals('Laravel is not loaded', $e->getMessage());
    }

    public function test_model_class_not_found()
    {
        $e = LaravelRelationManagerException::modelClassNotFound('Model');
        $this->assertEquals('Model class "Model" not found', $e->getMessage());
    }
}
