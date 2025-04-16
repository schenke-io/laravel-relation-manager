<?php

namespace SchenkeIo\LaravelRelationManager\Exceptions;

class LaravelNotLoadedException extends \Exception
{
    public function __construct(string $message = 'Laravel is not loaded')
    {
        parent::__construct($message);
    }
}
