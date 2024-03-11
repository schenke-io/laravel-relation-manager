<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Data;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Exceptions\LaravelNotLoadedException;
use Workbench\App\Models\Country;

class ClassDataTest extends TestCase
{
    /**
     * @throws LaravelNotLoadedException
     */
    public function testGetModelRelations()
    {
        // we use the missing database in unit tests for this test
        $this->expectException(LaravelNotLoadedException::class);
        ClassData::getRelationCountOfModel(Country::class);
    }
}
