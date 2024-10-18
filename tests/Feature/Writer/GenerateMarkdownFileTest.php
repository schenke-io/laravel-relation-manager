<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Writer;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Mockery;
use SchenkeIo\LaravelRelationManager\Define\ProjectContainer;
use SchenkeIo\LaravelRelationManager\Enums\Relations;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use SchenkeIo\LaravelRelationManager\Writer\GenerateMarkdownFile;
use Workbench\App\Models\Capital;
use Workbench\App\Models\Country;
use Workbench\App\Models\GeoRegion;
use Workbench\App\Models\Location;
use Workbench\App\Models\Single;

class GenerateMarkdownFileTest extends TestCase
{
    public function testWriteFileOk()
    {
        ProjectContainer::clear();
        ProjectContainer::addRelation(Country::class, Capital::class, Relations::hasOne);
        ProjectContainer::addRelation(Country::class, GeoRegion::class, Relations::noRelation);
        ProjectContainer::addRelation(Country::class, Location::class, Relations::morphMany);
        ProjectContainer::addRelation(Single::class, '', Relations::isSingle);

        $mockFilesystem = Mockery::mock(Filesystem::class);
        $mockFilesystem->shouldReceive('put')->once();

        $generator = new GenerateMarkdownFile($mockFilesystem);
        $return = $generator->writeFile(new Command, true);
        $this->assertNull($return);
    }

    public function testWriteFileException()
    {
        ProjectContainer::clear();
        ProjectContainer::addRelation(Country::class, Capital::class, Relations::hasOne);

        $mockFilesystem = Mockery::mock(Filesystem::class);
        $mockFilesystem->shouldReceive('put')->once()->andThrow(Exception::class, 'test error');

        $generator = new GenerateMarkdownFile($mockFilesystem);
        $return = $generator->writeFile(new Command, true);

        $this->assertIsString($return);
    }

    public function testMarkdownFileNotDefined()
    {
        ProjectContainer::clear();
        Config::set(ProjectContainer::CONFIG_KEY_MARKDOWN_FILE, '');
        ProjectContainer::addRelation(Country::class, Capital::class, Relations::hasOne);

        $mockFilesystem = Mockery::mock(Filesystem::class);

        $generator = new GenerateMarkdownFile($mockFilesystem);
        $return = $generator->writeFile(new Command, true);

        $this->assertIsString($return);

    }

    public function testDiagramGraphviz()
    {
        ProjectContainer::clear();
        Config::set(ProjectContainer::CONFIG_KEY_USE_MERMAID_DIAGRAMM, true);
        ProjectContainer::addRelation(Country::class, Capital::class, Relations::hasOne);

        $mockFilesystem = Mockery::mock(Filesystem::class);
        $mockFilesystem->shouldReceive('put')->once();
        $generator = new GenerateMarkdownFile($mockFilesystem);
        $return = $generator->writeFile(new Command, true);

        $this->assertNull($return);
    }
}
