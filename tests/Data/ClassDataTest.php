<?php

namespace SchenkeIo\LaravelRelationshipManager\Tests\Data;

use Illuminate\Foundation\Testing\RefreshDatabase;
use SchenkeIo\LaravelRelationshipManager\Data\ClassData;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Capital;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\City;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Country;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Region;
use SchenkeIo\LaravelRelationshipManager\Tests\database\Models\Single;
use SchenkeIo\LaravelRelationshipManager\Tests\TestCase;

class ClassDataTest extends TestCase
{
    use RefreshDatabase;

    public function testIsModel()
    {
        $this->assertTrue(ClassData::take(Country::class)->isModel);
        $this->assertFalse(ClassData::take('')->isModel);
        $this->assertFalse(ClassData::take(ClassData::class)->isModel);
    }

    public function testIsClass()
    {
        $this->assertTrue(ClassData::take(Country::class)->isClass);
        $this->assertFalse(ClassData::take('')->isClass);
        $this->assertTrue(ClassData::take(ClassData::class)->isClass);
    }

    public function testNewFromFilename()
    {
        $this->assertTrue(ClassData::newFromFileName(__FILE__)->isClass);
    }

    public function testGetFileAge()
    {
        $this->assertGreaterThan(0, ClassData::take(Country::class)->getFileAge());
        $this->assertEquals(-1, ClassData::take('')->getFileAge());
    }

    public function testIsFresherOrEqualThan(): void
    {
        $this->assertFalse(ClassData::take('')->isFresherOrEqualThan(''));
        $this->assertFalse(ClassData::take(Single::class)->isFresherOrEqualThan(''));
        $this->assertFalse(ClassData::take('')->isFresherOrEqualThan(Single::class));
        $this->assertTrue(ClassData::take(Single::class)->isFresherOrEqualThan(Single::class));
    }

    public function testGetModelRelations()
    {
        $this->assertEquals(
            [Capital::class, Region::class, City::class],
            array_keys(
                ClassData::take(Country::class)->getModelRelations()
            )
        );
    }

    public static function dataProviderGetShortNameWithClass(): array
    {
        return [
            'data 1' => ['', true, ''],
            'data 2' => [Country::class, false, 'Country'],
            'data 3' => [Country::class, true, 'Country::class'],
        ];
    }

    /**
     * @dataProvider dataProviderGetShortNameWithClass
     *
     * @return void
     */
    public function testGetShortNameWithClass(string $classname, bool $withClass, string $return)
    {
        $this->assertEquals($return, (new ClassData($classname))->getShortNameWithClass($withClass));
    }

    public function testGetFileBase()
    {
        $this->assertEquals(
            basename(__FILE__),
            ClassData::take(__CLASS__)->getFileBase()
        );
    }
}
