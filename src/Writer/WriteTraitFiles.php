<?php

namespace SchenkeIo\LaravelRelationManager\Writer;

use Illuminate\Filesystem\Filesystem;
use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Traits\AssertModelRelations;
use SchenkeIo\LaravelRelationManager\Traits\RelationTypes;

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
