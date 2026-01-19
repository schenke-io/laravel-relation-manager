<?php

namespace Tests\Unit\Support;

use SchenkeIo\LaravelRelationManager\Support\PathResolver;

uses()->group('support');

it('corrects the base path when inside orchestra testbench', function () {
    $fakeRoot = '/home/user/project';
    $orchestraPath = $fakeRoot.'/vendor/orchestra/testbench-core/laravel';

    PathResolver::$mockBasePath = $orchestraPath;

    expect(PathResolver::getRealBasePath())->toBe($fakeRoot);

    PathResolver::$mockBasePath = null;
});

it('does not change the base path when not in orchestra', function () {
    $fakeRoot = '/home/user/project';

    PathResolver::$mockBasePath = $fakeRoot;

    expect(PathResolver::getRealBasePath())->toBe($fakeRoot);

    PathResolver::$mockBasePath = null;
});
