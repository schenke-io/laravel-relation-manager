<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Console;

use Illuminate\Console\Command;
use SchenkeIo\LaravelRelationManager\Console\RelationshipBuilder;

class DummyCommand extends Command
{
    use RelationshipBuilder;
}
