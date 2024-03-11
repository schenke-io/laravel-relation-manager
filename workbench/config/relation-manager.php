<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel Relation Manager
    |--------------------------------------------------------------------------
    | Here you can define project-specific settings that are closely aligned
    | with your directory structure:
    |
    */

    /*
     *  Specifies the namespace for your project's models (commonly 'App\Models')
     */
    'modelNameSpace' => 'Workbench\App\Models',

    /*
     * Name of the test class (as a string or in 'Classname::class' format).
     * Ensure this class exists, as it will be overwritten.
     * For initial setup, an empty class can be used.
     */
    'projectTestClass' => 'SchenkeIo\LaravelRelationManager\Tests\Application\TestProjectTest',

    /*
     * Class that the project test class extends from (typically 'Tests\Test',
     * but you can choose another existing class).
     */
    'extendedTestClass' => 'SchenkeIo\LaravelRelationManager\Tests\TestCase',

    /*
     * Name of the file where documentation will be stored.
     */
    'markdownFile' => __DIR__.'/../docs/relations.md',

    /*
     * The base command used to run tests.
     * Specify the core command here only,
     * excluding additional arguments or filters.
     */
    'testCommand' => 'vendor/bin/pest',

];
