<?php

namespace SchenkeIo\LaravelRelationshipManager\Composer;

use SchenkeIo\LaravelRelationshipManager\Data\ClassData;
use SchenkeIo\LaravelRelationshipManager\Define\RelationshipEnum;
use SchenkeIo\LaravelRelationshipManager\Define\RelationshipsForPrimaryModel;
use SchenkeIo\LaravelRelationshipManager\Exceptions\DirectoryNotWritableException;
use SchenkeIo\LaravelRelationshipManager\Phpunit\AssertModelRelationships;
use SchenkeIo\LaravelRelationshipManager\Writer\GenerateAssertModelRelationshipsTrait;
use SchenkeIo\LaravelRelationshipManager\Writer\GenerateRelationshipsForPrimaryModelTrait;
use SchenkeIo\LaravelRelationshipManager\Writer\SaveFileContent;

/**
 * is called by composer
 * SchenkeIo\\LaravelRelationshipManager\\Composer\\ComposerWriteFilesCommand::run
 */
class ComposerWriteFilesCommand
{
    /**
     * static method called by composer
     *
     * @throws DirectoryNotWritableException
     */
    public static function run(mixed $handler): void
    {
        /*
         * during tests we set handler with a mock
         * in active use $handler becomes a composer object we dont need
         */
        if (! $handler instanceof SaveFileContent) {
            /*
             * we are in composer mode
             */
            $handler = new SaveFileContent();
        }
        $handler->saveContent(
            ClassData::take(AssertModelRelationships::class)->fileName,
            GenerateAssertModelRelationshipsTrait::getContent()
        );
        $handler->saveContent(
            ClassData::take(RelationshipsForPrimaryModel::class)->fileName,
            GenerateRelationshipsForPrimaryModelTrait::getContent()
        );
    }

    /**
     * -----------------
     * test the age of files
     */
    public static function relationshipFileIsOk(): bool
    {
        return ClassData::take(RelationshipsForPrimaryModel::class)
            ->isFresherOrEqualThan(RelationshipEnum::class);
    }

    public static function assertFileIsOk(): bool
    {
        return ClassData::take(AssertModelRelationships::class)
            ->isFresherOrEqualThan(RelationshipEnum::class);
    }
}
