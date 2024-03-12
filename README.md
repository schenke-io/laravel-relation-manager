# Laravel Relation Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schenke-io/laravel-relation-manager.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-relation-manager)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/schenke-io/laravel-relation-manager/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/schenke-io/laravel-relation-manager/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/schenke-io/laravel-relation-manager.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-relation-manager)
[![Coverage](https://img.shields.io/endpoint?url=https://otterwise.app/badge/github/schenke-io/laravel-relation-manager/fca0812c-2c3e-42b9-9d81-713f2c20769b)](https://otterwise.app/github/schenke-io/laravel-relation-manager)

Developing complex Laravel applications with many models can be difficult. 
**Laravel Relation Manager** helps by bringing all your model relationships 
together. It creates tests to make sure they work and documents them for 
easy reference. This saves you time, improves code quality, 
and keeps your project organized.

## Installation

You can install the package via composer:

```bash
composer require schenke-io/laravel-relation-manager
```
Install the config file of the package:

```bash
php artisan releation-manager:install
```

## Usage

This package's core functionality is provided by two components:
1) **Configuration File**: The `config/relation-manager.php` file allows you to define directories, files and namespaces of your project.
2) **Custom Relation Manager Command**: This command, which extends the `RelationManagerCommand` class, facilitates the configuration process.

In the command file:
- define models and their relations
- decide if you want to add reverse relations
- add BackedEnum classes used in $casts array of models as a special relation
- after the model-relation definition:
  - write the test file 
  - run the test file (only)
  - write text and graphical documentation
  - echo tables 

```php
# app/Console/Commands/RelationWriteCommand

use SchenkeIo\LaravelRelationManager\Console\RelationManagerCommand;

class RelationWriteCommand extends RelationManagerCommand 
{
    
    public function handle(): void
    {       
        
        $this->relationManager->model('Country')
            ->hasOne('Capital', true)
            ->hasMany('Region', true)
            ->castEnum('Size');
            
        $this->relationManager->model('Region')
            ->hasMany('City', true);
            
                        
        // repeat this for any model    

        // finally 
        $this->relationManager->writeTest(false)
          ->writeMarkdown()
          ->runTest()
          ->showModelTable();
                   
        
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
