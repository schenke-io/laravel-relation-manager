<?php

use SchenkeIo\LaravelRelationManager\Data\ConfigData;

test('ConfigData has default values', function () {
    $config = new ConfigData;

    expect($config->markdownPath)->toEndWith('RELATIONS.md')
        ->and($config->modelPath)->toBe('app/Models');
});

test('ConfigData accepts custom values', function () {
    $config = new ConfigData(
        markdownPath: 'CUSTOM.md',
        modelPath: 'src/Models'
    );

    expect($config->markdownPath)->toBe('CUSTOM.md')
        ->and($config->modelPath)->toBe('src/Models');
});
