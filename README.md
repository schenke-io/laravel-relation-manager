# Simplify model relationships in Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schenke-io/laravel-relationship-manager.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-relationship-manager)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/schenke-io/laravel-relationship-manager/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/schenke-io/laravel-relationship-manager/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/schenke-io/laravel-relationship-manager/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/schenke-io/laravel-relationship-manager/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/schenke-io/laravel-relationship-manager.svg?style=flat-square)](https://packagist.org/packages/schenke-io/laravel-relationship-manager)

This package helps you to write better tests around model relationships in Laravel. It validates methods
and the database schema setup for all the modells.

## Installation

You can install the package via composer:

```bash
composer require schenke-io/laravel-relationship-manager
```

## Usage

### Writing manual tests 

You can enhance one of your tests with new assertions 
related to model relationships. 
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
