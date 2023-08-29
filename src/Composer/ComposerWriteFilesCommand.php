<?php

namespace SchenkeIo\LaravelRelationManager\Composer;

use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Define\RelationshipEnum;
use SchenkeIo\LaravelRelationManager\Define\RelationshipsForPrimaryModel;
use SchenkeIo\LaravelRelationManager\Exceptions\DirectoryNotWritableException;
use SchenkeIo\LaravelRelationManager\Phpunit\AssertModelRelationships;
use SchenkeIo\LaravelRelationManager\Writer\GenerateAssertModelRelationshipsTrait;
use SchenkeIo\LaravelRelationManager\Writer\GenerateRelationshipsForPrimaryModelTrait;
use SchenkeIo\LaravelRelationManager\Writer\SaveFileContent;

/**
 * is called by composer
 * SchenkeIo\\LaravelRelationManager\\Composer\\ComposerWriteFilesCommand::run
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
