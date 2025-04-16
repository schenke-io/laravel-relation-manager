<!--

This file was written by 'WriteMarkdownCommand.php' line 23 using
SchenkeIo\PackagingTools\Markdown\MarkdownAssembler

Do not edit manually as it will be overwritten.

-->
# Laravel Relation Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schenke-io/laravel-relation-manager.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-relation-manager)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/schenke-io/laravel-relation-manager/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/schenke-io/laravel-relation-manager/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/schenke-io/laravel-relation-manager.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-relation-manager)
[![Coverage](.github/coverage.svg)]()
[![PHPStan](.github/phpstan.svg)]()

Developing complex Laravel applications with many models can be difficult.
**Laravel Relation Manager** helps by bringing all your model relationships
together. It creates tests to make sure they work and documents them for
easy reference. This saves you time, improves code quality,
and keeps your project organized.



* [Laravel Relation Manager](#laravel-relation-manager)
  * [Installation](#installation)
  * [Configuration](#configuration)
  * [Usage](#usage)
  * [Testing](#testing)
  * [Changelog](#changelog)
  * [License](#license)



## Installation

You can install the package via composer:

```bash
composer require schenke-io/laravel-relation-manager
```
Install the config file of the package:

```bash
php artisan relation-manager:install
```





## Configuration

The configuration file `config/relation-manager.php` has the following keys available.




| key                          | definition                                       | type    |
|------------------------------|--------------------------------------------------|---------|
| modelNameSpace               | namespace of the models (commonly App\Models)    | String  |
| projectTestClass             | empty test class which will be (over)written     | String  |
| extendedTestClass            | class the written class should extend from       | String  |
| markdownFile                 | full path for the markdown file which is written | String  |
| testCommand                  | console command to run the tests                 | String  |
| useMermaidDiagram            | true = mermaid, false = Graphviz                 | Boolean |
| showPivotTablesInDiagram     | should pivot tables be shown in the diagram      | Boolean |
| refreshDatabaseAfterEachTest | should the database be refreshed after each test | Boolean |
| testDatabase                 | extend the tests to asserts of the database      | Boolean |




## Usage

This package's core functionality is provided by two components:
1) **Configuration File**: The `config/relation-manager.php` file allows you to define directories, files and namespaces of your project.
2) **Custom Relation Manager Command**: This command, which extends the `RelationManagerCommand` class, facilitates the configuration process.

In the command file:
- define models and their relations
- decide if you want to add reverse relations
- it is possible to have more than one relationship between two models 
- after the model-relation definition:
    - write the test file
    - run the test file (only)
    - write text and graphical documentation
    - echo tables

```php
// app/Console/Commands/RelationWriteCommand

use SchenkeIo\LaravelRelationManager\Console\RelationManagerCommand;

class RelationWriteCommand extends RelationManagerCommand 
{
    
    public function handle(): void
    {       
        
        $this->relationManager->model('Country')
            ->hasOne('Capital', true)
            ->hasMany('Region', true);
            
        $this->relationManager->model('Region')
            ->hasMany('City', true);
            
                        
        // repeat this for any model    

        // finally 
        $this->relationManager
            ->writeMarkdown()
            ->showTables()
            ->writeTest(strict: true)
            ->runTest();
                   
        
    }    
}

```

This command is called by default with `php artisan relation-manager:run`.

The following methods can be used inside `handle()`:

| method             | parameter                                          | details                                                      |
|--------------------|----------------------------------------------------|--------------------------------------------------------------|
| model($model)      | name of the model                                  | the model name is relative to the model namespace configured |
| writeTest($strict) | false: define the minimum, true: define everything | write the test file as defined                               |
| runTest            | -                                                  | run the test file                                            |
| writeMarkdown      | -                                                  | write a markdown file with the documentation                 |
| showTables         | -                                                  | Show the information as a table in the console               |
|                    |                                                    |                                                              |




## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.



