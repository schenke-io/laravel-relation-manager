<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Data;

use Illuminate\Contracts\Container\BindingResolutionException;
use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Data\ClassData;
use Workbench\App\Models\Country;

class ClassDataTest extends TestCase
{
    public function testGetModelRelations()
    {
        // we use the missing database in unit tests for this test
        $this->expectException(BindingResolutionException::class);
        ClassData::getRelationCountOfModel(Country::class);
    }
}
