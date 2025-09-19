<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Data;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Metadata\ReflectionException;
use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Exceptions\LaravelNotLoadedException;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use Workbench\App\Models\Capital;
use Workbench\App\Models\City;
use Workbench\App\Models\Country;
use Workbench\App\Models\Highway;
use Workbench\App\Models\Location;
use Workbench\App\Models\Region;
use Workbench\App\Models\Single;

class ClassDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_wrong_class_returns_empty_model_relations()
    {
        // take a non-existent class and check if getModelRelations is []
        $this->assertEquals([], ClassData::take('Vendor\\Package\\FooBarBazNonExisting')->getModelRelations());

        // take an existing non-model class and check if getModelRelations is []
        $this->assertEquals([], ClassData::take(ClassData::class)->getModelRelations());
    }

    public function test_is_model()
    {
        $this->assertTrue(ClassData::take(Country::class)->isModel);
        $this->assertFalse(ClassData::take('')->isModel);
        $this->assertFalse(ClassData::take(ClassData::class)->isModel);
    }

    public function test_is_class()
    {
        $this->assertTrue(ClassData::take(Country::class)->isClass);
        $this->assertFalse(ClassData::take('')->isClass);
        $this->assertTrue(ClassData::take(ClassData::class)->isClass);
    }

    public function test_get_file_age()
    {
        $this->assertGreaterThan(0, ClassData::take(Country::class)->getFileAge());
        $this->assertEquals(-1, ClassData::take('')->getFileAge());
    }

    public function test_is_fresher_or_equal_than(): void
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
     * @throws ReflectionException
     * @throws LaravelNotLoadedException
     */
    public function test_get_model_relations()
    {
        $this->assertEquals(
            [Capital::class, City::class, Region::class],
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

    #[DataProvider('dataProviderGetShortNameWithClass')]
    /**
     * @return void
     */
    public function test_get_short_name_with_class(string $classname, string $return)
    {
        $this->assertEquals($return, (new ClassData($classname))->getShortName());
    }

    public function test_get_file_base()
    {
        $this->assertEquals(
            basename(__FILE__),
            ClassData::take(__CLASS__)->getFileBase()
        );
    }

    /**
     * @throws LaravelNotLoadedException
     */
    public function test_get_relation_count_of_model()
    {
        $this->assertEquals(-1, ClassData::getRelationCountOfModel(''));
        $this->assertEquals(4, ClassData::getRelationCountOfModel(Country::class));
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

    #[DataProvider('dataProviderRelationExpectations')]
    /**
     * @throws LaravelNotLoadedException
     */
    public function test_get_relation_expectations(string $class, string $returnType, string $usesClass, string $regex): void
    {
        $this->assertMatchesRegularExpression($regex, ClassData::getRelationExpectation($class, $returnType, $usesClass));
    }
}
