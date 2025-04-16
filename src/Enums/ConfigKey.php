<?php

namespace SchenkeIo\LaravelRelationManager\Enums;

use Illuminate\Support\Facades\Config;

enum ConfigKey: string
{
    case MODEL_NAME_SPACE = 'modelNameSpace';

    case PROJECT_TEST_CLASS = 'projectTestClass';

    case EXTENDED_TEST_CLASS = 'extendedTestClass';

    case MARKDOWN_FILE = 'markdownFile';

    case TEST_COMMAND = 'testCommand';

    case USE_MERMAID_DIAGRAMM = 'useMermaidDiagram';

    case SHOW_PIVOT_TABLES_IN_DIAGRAMM = 'showPivotTablesInDiagram';

    case REFRESH_DATABASE_AFTER_EACH_TEST = 'refreshDatabaseAfterEachTest';

    case TEST_DATABASE = 'testDatabase';

    public function full(): string
    {
        return 'relation-manager.'.$this->value;
    }

    public function get(mixed $default = null): mixed
    {
        return $this->type()->format(config($this->full(), $default));
    }

    public function set(mixed $value): void
    {
        Config::set($this->full(), $this->type()->format($value));
    }

    public function type(): Type
    {
        return match ($this) {
            self::MODEL_NAME_SPACE,
            self::PROJECT_TEST_CLASS,
            self::EXTENDED_TEST_CLASS,
            self::MARKDOWN_FILE,
            self::TEST_COMMAND => Type::String,

            self::USE_MERMAID_DIAGRAMM,
            self::TEST_DATABASE,
            self::REFRESH_DATABASE_AFTER_EACH_TEST,
            self::SHOW_PIVOT_TABLES_IN_DIAGRAMM => Type::Boolean
        };
    }

    public function definition(): string
    {
        return match ($this) {
            self::MODEL_NAME_SPACE => 'namespace of the models (commonly App\Models)',
            self::PROJECT_TEST_CLASS => 'empty test class which will be (over)written',
            self::EXTENDED_TEST_CLASS => 'class the written class should extend from',
            self::MARKDOWN_FILE => 'full path for the markdown file which is written',
            self::TEST_COMMAND => 'console command to run the tests',
            self::USE_MERMAID_DIAGRAMM => 'true = mermaid, false = Graphviz',
            self::TEST_DATABASE => 'extend the tests to asserts of the database',
            self::SHOW_PIVOT_TABLES_IN_DIAGRAMM => 'should pivot tables be shown in the diagram',
            self::REFRESH_DATABASE_AFTER_EACH_TEST => 'should the database be refreshed after each test' ,
        };
    }
}
