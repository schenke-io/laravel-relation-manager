<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Data;

use Illuminate\Foundation\Testing\RefreshDatabase;
use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Tests\database\Models\Capital;
use SchenkeIo\LaravelRelationManager\Tests\database\Models\City;
use SchenkeIo\LaravelRelationManager\Tests\database\Models\Country;
use SchenkeIo\LaravelRelationManager\Tests\database\Models\Region;
use SchenkeIo\LaravelRelationManager\Tests\database\Models\Single;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

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

        /*
         * we overwrite two valid class files in this directory with 2 seconds difference and measure it
         */

        $fileNameOld = __DIR__.'/FileOld.php';
        $fileNameNew = __DIR__.'/FileNew.php';
        $namespace = 'SchenkeIo\LaravelRelationManager\Tests\Data';
        file_put_contents($fileNameOld, "<?php namespace $namespace; class FileOld {}");
        sleep(2);
        file_put_contents($fileNameNew, "<?php namespace $namespace; class FileNew {}");

        $classOld = ClassData::take(FileOld::class);
        $classNew = ClassData::take(FileNew::class);

        $this->assertTrue($classNew->isFresherOrEqualThan(FileOld::class));
        $this->assertTrue($classNew->isFresherOrEqualThan(FileNew::class));
        $this->assertFalse($classOld->isFresherOrEqualThan(FileNew::class));
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
