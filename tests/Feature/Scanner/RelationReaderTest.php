<?php

use SchenkeIo\LaravelRelationManager\Enums\ConfigKey;
use SchenkeIo\LaravelRelationManager\Scanner;

use function Orchestra\Testbench\workbench_path;

it('can scan the demo project and displays the code', function () {
    $scanner = new Scanner\RelationReader;
    $code = $scanner->displayRelations();

    expect(strlen($code))->toBeGreaterThan(100);
});

it('returns empty string when directory value is invalid', function () {
    // Point to a non-existent directory to simulate invalid configuration
    ConfigKey::MODEL_DIRECTORY->set(workbench_path('app/Models/does-not-exist'));

    $scanner = new Scanner\RelationReader;
    $code = $scanner->displayRelations();

    expect($code)->toBe('');
});

it('skips files that can not be instantiated as models', function () {
    // Create a temporary directory with a PHP file that will NOT be autoloadable
    $dummyDir = workbench_path('test');

    // Point the scanner to this directory and namespace that isn't autoloaded
    ConfigKey::MODEL_DIRECTORY->set($dummyDir);
    ConfigKey::MODEL_NAME_SPACE->set('test\\\\Models');

    $scanner = new Scanner\RelationReader;
    $code = $scanner->displayRelations();

    // Since the class is not auto-loadable and not a Model, it should be skipped and return empty
    expect($code)->toBe('');

});

it('ignores unknown relation names', function () {
    // Create a fake reader that returns an unknown relation name
    $reader = new class extends Scanner\RelationReader
    {
        public function relationData(): array
        {
            return [
                'App\\Models\\Foo' => [
                    'App\\Models\\Bar' => ['someUnknownRelation'],
                ],
            ];
        }
    };

    $code = $reader->displayRelations();

    // Unknown relation names are ignored; no output should be generated
    expect($code)->toBe('');
});

it('handles ReflectionClass exceptions in isInstantiableModel by returning false', function () {
    $reader = new class extends Scanner\RelationReader
    {
        public function callIsInstantiable(string $class): bool
        {
            return $this->isInstantiableModel($class);
        }
    };

    // Pass a non-existent class to trigger ReflectionClass exception
    expect($reader->callIsInstantiable('Not\\Existing\\Anything'))
        ->toBeFalse();
});

it('continues when ClassData throws inside relationData loop', function () {
    // Force the reader to think an invalid class is instantiable, so ClassData will throw
    $reader = new class extends Scanner\RelationReader
    {
        protected function getModelPaths(): array
        {
            return ['/tmp/dummy.php'];
        }

        protected function getClassFromPath(string $path): ?string
        {
            return 'Not\\A\\Real\\Model';
        }

        protected function isInstantiableModel(string $class): bool
        {
            return true; // pretend it's a valid, instantiable model
        }
    };

    // relationData should catch the Throwable and simply skip, returning an empty array
    expect($reader->relationData())->toBe([]);

    // and displayRelations consequently returns an empty string
    expect($reader->displayRelations())->toBe('');
});
