# Simplify model relations in Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schenke-io/laravel-relation-manager.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-relation-manager)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/schenke-io/laravel-relation-manager/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/schenke-io/laravel-relation-manager/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/schenke-io/laravel-relation-manager.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-relation-manager)
[![Coverage](https://img.shields.io/endpoint?url=https://otterwise.app/badge/github/schenke-io/laravel-relation-manager/fca0812c-2c3e-42b9-9d81-713f2c20769b)](https://otterwise.app/github/schenke-io/laravel-relation-manager)

This package simplifies managing complex Laravel projects 
with many models and relationships. It offers:

+ **Centralized documentation:** Easily keep track of all model relationships in one place.
+ **Improved code quality:** Encourages test-driven development and ensures defined relationships are actually implemented.
+ **Automated tasks:**
  + Lists all models and their relationships.
  + Generates test files to verify relationships exist.
  + Creates documentation files for defined relationships.

This package streamlines your workflow, saving you time 
and effort while maintaining clean and well-documented code.

## Installation

You can install the package via composer:

```bash
composer require schenke-io/laravel-relation-manager
```

## Usage

The package works central with a console command 
which when called writes the files and run the test.

```php
# app/Console/Commands/RelationWriteCommand

use SchenkeIo\LaravelRelationManager\Facades\Relations;

class RelationWriteCommand extends Command 
{
    protected $signature = 'relation:write';
    protected $description = 'define and write the model relations';
    
    public function handle(): void
    {       
        Relations::config(
            command: $this,
            modelNameSpace: 'App\Models'
        );
        
        Relations::model('Country')
            ->hasOne('Capital', true)
            ->hasMany('Region', true);
        
        Relations::writeTest(
            testClass: 'Tests\Feature\ProjectRelationTest',
            extendedTestClass: 'Tests\Test', 
            strict: false
        )
            ->writeMarkdown(base_path('docu/relations.md'))
            ->runTest();
            ->showTable();
    }    
}

```

### Writing manual tests 

You can enhance one of your tests with new assertions 
related to model relations. 
They will verify if the models have the 
relations setup and are working with the database well (e.g. migrations).


In this example the test verifies the following:
+ `Country::class` exists and is a model
+ `Capital::class` exists and is a model
+ in `Country::class` is a HasOne-relation to `Capital::class` which works with the database 


```php
# tests/Feature/ModelRelationTest.php 
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use SchenkeIo\LaravelRelationshipManager\Phpunit\AssertModelRelationships;
use App\Models\Country;
use App\Models\Capital;
use App\Models\Regions;

class ModelRelationTest extends TestCase 
{
    use RefreshDatabase;
    use AssertModelRelationships;
    
    public function testCountryRelationships()
    {
        $this->assertModelHasOne(Country::class, Capital::Class);
    }
}

```
Another way to write the test is using full class names like:
```php

...

        $this->assertModelHasOne('App\Models\Country', 'App\Models\Capital');

...        

```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
