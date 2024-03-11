<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Writer;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use SchenkeIo\LaravelRelationManager\Define\ProjectContainer;
use SchenkeIo\LaravelRelationManager\Define\RelationsEnum;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use SchenkeIo\LaravelRelationManager\Writer\GenerateMarkdownFile;
use Workbench\App\Models\Capital;
use Workbench\App\Models\Country;
use Workbench\App\Models\Location;
use Workbench\App\Models\Region;
use Workbench\App\Models\Single;

class GenerateMarkdownFileTest extends TestCase
{
    public function testWriteFileOk()
    {
        ProjectContainer::clear();
        ProjectContainer::addRelation(Country::class, Capital::class, RelationsEnum::hasOne);
        ProjectContainer::addRelation(Country::class, Region::class, RelationsEnum::noRelation);
        ProjectContainer::addRelation(Country::class, Location::class, RelationsEnum::morphMany);
        ProjectContainer::addRelation(Single::class, '', RelationsEnum::isSingle);

        $mockFilesystem = Mockery::mock(Filesystem::class);
        $mockFilesystem->shouldReceive('put')->once();

        $generator = new GenerateMarkdownFile($mockFilesystem);
        $return = $generator->writeFile(new Command(), true);
        $this->assertNull($return);
    }

    public function testWriteFileException()
    {
        ProjectContainer::clear();
        ProjectContainer::addRelation(Country::class, Capital::class, RelationsEnum::hasOne);

        $mockFilesystem = Mockery::mock(Filesystem::class);
        $mockFilesystem->shouldReceive('put')->once()->andThrow(Exception::class, 'test error');

        $generator = new GenerateMarkdownFile($mockFilesystem);
        $return = $generator->writeFile(new Command(), true);

        $this->assertIsString($return);
    }
}
