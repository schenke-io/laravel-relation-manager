<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Writer;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use SchenkeIo\LaravelRelationManager\Define\ProjectContainer;
use SchenkeIo\LaravelRelationManager\Enums\ConfigKey;
use SchenkeIo\LaravelRelationManager\Enums\Relations;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use SchenkeIo\LaravelRelationManager\Writer\GenerateMarkdownFile;
use Workbench\App\Models\Capital;
use Workbench\App\Models\Country;
use Workbench\App\Models\Location;
use Workbench\App\Models\Region;
use Workbench\App\Models\Single;

class GenerateMarkdownFileTest extends TestCase
{
    public function test_write_file_ok()
    {
        ProjectContainer::clear();
        ProjectContainer::addRelation(Country::class, Capital::class, Relations::hasOne);
        ProjectContainer::addRelation(Country::class, Region::class, Relations::noRelation);
        ProjectContainer::addRelation(Country::class, Location::class, Relations::morphMany);
        ProjectContainer::addRelation(Single::class, '', Relations::isSingle);

        $mockFilesystem = Mockery::mock(Filesystem::class);
        $mockFilesystem->shouldReceive('put')->once();

        $generator = new GenerateMarkdownFile($mockFilesystem);
        $return = $generator->writeFile(new Command, true);
        $this->assertNull($return);
    }

    public function test_write_file_exception()
    {
        ProjectContainer::clear();
        ProjectContainer::addRelation(Country::class, Capital::class, Relations::hasOne);

        $mockFilesystem = Mockery::mock(Filesystem::class);
        $mockFilesystem->shouldReceive('put')->once()->andThrow(Exception::class, 'test error');

        $generator = new GenerateMarkdownFile($mockFilesystem);
        $return = $generator->writeFile(new Command, true);

        $this->assertIsString($return);
    }

    public function test_markdown_file_not_defined()
    {
        ProjectContainer::clear();
        ConfigKey::MARKDOWN_FILE->set('');
        ProjectContainer::addRelation(Country::class, Capital::class, Relations::hasOne);

        $mockFilesystem = Mockery::mock(Filesystem::class);

        $generator = new GenerateMarkdownFile($mockFilesystem);
        $return = $generator->writeFile(new Command);

        $this->assertIsString($return);

    }

    public function test_diagram_graphviz()
    {
        ProjectContainer::clear();
        ConfigKey::USE_MERMAID_DIAGRAMM->set(true);
        ProjectContainer::addRelation(Country::class, Capital::class, Relations::hasOne);

        $mockFilesystem = Mockery::mock(Filesystem::class);
        $mockFilesystem->shouldReceive('put')->once();
        $generator = new GenerateMarkdownFile($mockFilesystem);
        $return = $generator->writeFile(new Command, true);

        $this->assertNull($return);
    }
}
