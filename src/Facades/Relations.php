<?php

namespace SchenkeIo\LaravelRelationManager\Facades;

use Illuminate\Support\Facades\Facade;
use SchenkeIo\LaravelRelationManager\Define\RelationsHandler;

class Relations extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return RelationsHandler::class;
    }
}
