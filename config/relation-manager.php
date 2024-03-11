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
    'modelNameSpace' => 'App\Models',

    /*
     * Name of the test class (as a string or in 'Classname::class' format).
     * Ensure this class exists, as it will be overwritten.
     * For initial setup, an empty class can be used.
     */
    'projectTestClass' => 'Tests\Feature\RelationArchitectureTest',

    /*
     * Class that the project test class extends from (typically 'Tests\Test',
     * but you can choose another existing class).
     */
    'extendedTestClass' => 'Tests\TestCase',

    /*
     * Name of the file where documentation will be stored.
     */
    'markdownFile' => base_path('docs/relation.md'),

    /*
     * The base command used to run tests.
     * Specify the core command here only,
     * excluding additional arguments or filters.
     */
    'testCommand' => 'php artisan test',

];
