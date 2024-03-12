<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Data;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Exceptions\LaravelNotLoadedException;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use Workbench\App\Models\Capital;
use Workbench\App\Models\City;
use Workbench\App\Models\Country;
use Workbench\App\Models\Highway;
use Workbench\App\Models\Location;
use Workbench\App\Models\GeoRegion;
use Workbench\App\Models\Single;

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
        // same class
        $this->assertTrue(ClassData::take(Single::class)->isFresherOrEqualThan(Single::class));

        // mock
        $mockFileSystem = Mockery::mock(Filesystem::class);
        $mockFileSystem->shouldReceive('exists')->once()->andReturn(true);
        $mockFileSystem->shouldReceive('lastModified')
            ->once()
            ->with(ClassData::take(Country::class)->fileName)->andReturn(100000000000000000);

        $classData = new ClassData(Country::class, $mockFileSystem);
        // compare real datetime with the mocked extreme time in future
        $this->assertTrue($classData->isFresherOrEqualThan(Capital::class));
    }

    /**
     * @throws \ReflectionException
     * @throws LaravelNotLoadedException
     */
    public function testGetModelRelations()
    {
        $this->assertEquals(
            [Capital::class, GeoRegion::class, City::class],
            array_keys(
                ClassData::take(Country::class)->getModelRelations()
            )
        );
        $this->assertEquals([], ClassData::take('')->getModelRelations());
    }

    public static function dataProviderGetShortNameWithClass(): array
    {
        return [
            'data 1' => ['', ''],
            'data 2' => [Country::class, 'Country'],
            'data 3' => [Country::class, 'Country'],
        ];
    }

    /**
     * @dataProvider dataProviderGetShortNameWithClass
     *
     * @return void
     */
    public function testGetShortNameWithClass(string $classname, string $return)
    {
        $this->assertEquals($return, (new ClassData($classname))->getShortName());
    }

    public function testGetFileBase()
    {
        $this->assertEquals(
            basename(__FILE__),
            ClassData::take(__CLASS__)->getFileBase()
        );
    }

    /**
     * @throws LaravelNotLoadedException
     */
    public function testGetRelationCountOfModel()
    {
        $this->assertEquals(-1, ClassData::getRelationCountOfModel(''));
        $this->assertEquals(3, ClassData::getRelationCountOfModel(Country::class));
    }

    public static function dataProviderRelationExpectations(): array
    {
        return [
            'ok 1' => [Country::class, 'HasOne', Capital::class, '//'],
            'ok 2' => [Location::class, 'MorphTo', Location::class, '//'],
            'relation not found' => [Country::class, 'HasOne', Highway::class, '/.{3,100}/'],
            'relation wrong' => [Country::class, 'hasOne', Highway::class, '/.{3,100}/'],
            'relation twice' => [Country::class, 'hasOne', Capital::class, '/.{3,100}/'],
        ];
    }

    /**
     * @dataProvider dataProviderRelationExpectations
     *
     * @throws LaravelNotLoadedException
     */
    public function testGetRelationExpectations(string $class, string $returnType, string $usesClass, string $regex): void
    {
        $this->assertMatchesRegularExpression($regex, ClassData::getRelationExpectation($class, $returnType, $usesClass));
    }
}
