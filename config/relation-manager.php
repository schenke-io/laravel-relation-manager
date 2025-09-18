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
     *  Specifies the absolute path for your project's model files (commonly app_path('Models'))
     */
    'modelDirectory' => app_path('Models'),

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

    /*
     * Relationship Diagram Generation:
     *
     * This setting controls how relationship diagrams are included in markdown documentation:
     * - true:  Diagrams are rendered as inline Mermaid code. Users viewing the markdown
     *          will need the Mermaid plugin installed to see the diagrams.
     * - false: Diagrams are generated as separate SVG files using Graphviz. This requires
     *          Graphviz to be installed on the system generating the documentation.
     */
    'useMermaidDiagram' => true,

    /*
     * When true the existence of tables and full fields in the database is tested
     */
    'testDatabase' => true,

    /*
     * should pivot tables be shown in the diagram
     */
    'showPivotTablesInDiagram' => true,

    /*
     * should the database be refreshed after each test
     */
    'refreshDatabaseAfterEachTest' => false,
];
