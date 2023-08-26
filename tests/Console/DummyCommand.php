<?php

namespace SchenkeIo\LaravelRelationshipManager\Tests\Console;

use Illuminate\Console\Command;
use SchenkeIo\LaravelRelationshipManager\Console\RelationshipBuilder;

class DummyCommand extends Command
{
    use RelationshipBuilder;
}
