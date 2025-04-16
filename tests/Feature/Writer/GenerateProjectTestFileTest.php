<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Writer;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use SchenkeIo\LaravelRelationManager\Define\ProjectContainer;
use SchenkeIo\LaravelRelationManager\Enums\ConfigKey;
use SchenkeIo\LaravelRelationManager\Enums\Relation;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use SchenkeIo\LaravelRelationManager\Writer\GenerateProjectTestFile;
use Workbench\App\Console\Commands\RunTestProjectManagerCommand;
use Workbench\App\Models\Capital;
use Workbench\App\Models\Country;

class GenerateProjectTestFileTest extends TestCase
{
    public function test_get_test_group()
    {
        $this->assertEquals('GenerateProjectTestFile', GenerateProjectTestFile::testGroup());
    }

    public function test_write_file_ok()
    {
        ProjectContainer::clear();
        (new RunTestProjectManagerCommand)->buildRelations();
        ConfigKey::TEST_DATABASE->set(true);
        $mockFilesystem = Mockery::mock(Filesystem::class);
        $mockFilesystem->shouldReceive('put')->once();

        $generator = new GenerateProjectTestFile($mockFilesystem);
        $return = $generator->writeFile(new Command, true);
        $this->assertNull($return);
    }

    public function test_write_file_exception()
    {
        ProjectContainer::clear();
        ProjectContainer::addRelation(Country::class, Capital::class, Relation::hasOne);

        $mockFilesystem = Mockery::mock(Filesystem::class);
        $mockFilesystem->shouldReceive('put')->once()->andThrow(Exception::class, 'test error');

        $generator = new GenerateProjectTestFile($mockFilesystem);
        $return = $generator->writeFile(new Command, true);

        $this->assertIsString($return);
    }
}
