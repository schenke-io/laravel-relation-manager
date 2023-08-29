<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Data;

use Illuminate\Foundation\Testing\RefreshDatabase;
use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Demo\Models\Capital;
use SchenkeIo\LaravelRelationManager\Demo\Models\City;
use SchenkeIo\LaravelRelationManager\Demo\Models\Country;
use SchenkeIo\LaravelRelationManager\Demo\Models\Region;
use SchenkeIo\LaravelRelationManager\Demo\Models\Single;
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
        $timeOld = time() - 100;
        $timeNew = $timeOld + 10;
        touch(__DIR__.'/FileOld.php', $timeOld, $timeOld);
        touch(__DIR__.'/FileNew.php', $timeNew, $timeNew);

        //        $this->writeClass('FileOld');
        //        sleep(2);
        //        $this->writeClass('FileNew');

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
