<?php

use SchenkeIo\LaravelRelationshipManager\Composer\ComposerWriteFilesCommand;

it('can verify if AssertModelRelationships is outdated', function () {
    expect(ComposerWriteFilesCommand::assertFileIsOk())->toBeBool();
});

it('can verify if Relationships is outdated', function () {
    expect(ComposerWriteFilesCommand::relationshipFileIsOk())->toBeBool();
});
