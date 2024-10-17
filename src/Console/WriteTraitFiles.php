<?php

namespace SchenkeIo\LaravelRelationManager\Console;

use Illuminate\Filesystem\Filesystem;
use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Define\RelationTypes;
use SchenkeIo\LaravelRelationManager\Phpunit\AssertModelRelations;
use SchenkeIo\LaravelRelationManager\Writer\GenerateAssertModelRelationsTrait;
use SchenkeIo\LaravelRelationManager\Writer\GenerateRelationTypesTrait;

class WriteTraitFiles
{
    public function __construct(protected Filesystem $fileSystem) {}

    public function generate(): void
    {

        $filename = ClassData::take(AssertModelRelations::class)->fileName;
        $this->fileSystem->put($filename, GenerateAssertModelRelationsTrait::getContent());

        $filename = ClassData::take(RelationTypes::class)->fileName;
        $this->fileSystem->put($filename, GenerateRelationTypesTrait::getContent());

    }
}
