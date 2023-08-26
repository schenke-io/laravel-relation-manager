<?php

use SchenkeIo\LaravelRelationManager\Composer\ComposerWriteFilesCommand;

it('can verify if AssertModelRelationships is outdated', function () {
    expect(ComposerWriteFilesCommand::assertFileIsOk())->toBeTrue();
});

it('can verify if Relationships is outdated', function () {
    expect(ComposerWriteFilesCommand::relationshipFileIsOk())->toBeTrue();
});
