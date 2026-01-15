<?php

namespace SchenkeIo\LaravelRelationManager\Exceptions;

use Exception;

/**
 * Central exception class for the Laravel Relation Manager package,
 * featuring static constructors for common error scenarios.
 */
class LaravelRelationManagerException extends Exception
{
    public static function directoryNotFound(string $directory): self
    {
        return new self(sprintf('Directory "%s" not found', $directory));
    }

    public static function directoryNotWritable(string $directory): self
    {
        return new self(sprintf('Directory "%s" is not writable', $directory));
    }

    public static function invalidClass(string $class): self
    {
        return new self(sprintf('Class "%s" is invalid', $class));
    }

    public static function laravelNotLoaded(): self
    {
        return new self('Laravel is not loaded');
    }

    public static function modelClassNotFound(string $modelClass): self
    {
        return new self(sprintf('Model class "%s" not found', $modelClass));
    }
}
