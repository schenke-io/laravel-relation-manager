<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Scanner;

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Scanner\ModelScanner;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class ModelScannerExtraTest extends TestCase
{
    public function test_scan_throws_exception_if_directory_missing()
    {
        File::shouldReceive('isDirectory')->andReturn(false);
        $this->expectException(\SchenkeIo\LaravelRelationManager\Exceptions\LaravelRelationManagerException::class);
        $scanner = new ModelScanner;
        $scanner->scan('non-existent');
    }

    public function test_scan_skips_non_php_files()
    {
        File::shouldReceive('isDirectory')->andReturn(true);
        File::shouldReceive('allFiles')->andReturn([
            new \Symfony\Component\Finder\SplFileInfo('test.txt', 'relative', 'test.txt'),
        ]);
        File::shouldReceive('get')->andReturn('not php code');
        $scanner = new ModelScanner;
        $this->assertEquals([], $scanner->scan('dir'));
    }

    public function test_scan_skips_files_not_containing_model_class()
    {
        File::shouldReceive('isDirectory')->andReturn(true);
        File::shouldReceive('allFiles')->andReturn([
            new \Symfony\Component\Finder\SplFileInfo('NotAModel.php', '', 'NotAModel.php'),
        ]);
        File::shouldReceive('get')->andReturn('<?php echo "hello";');

        $scanner = new ModelScanner;
        $this->assertEquals([], $scanner->scan('dir'));
    }

    public function test_scan_handles_mixed_models()
    {
        $scanner = new ModelScanner;
        $results = $scanner->scan(__DIR__.'/../../Models');

        $this->assertArrayHasKey(\SchenkeIo\LaravelRelationManager\Tests\Models\MixedModel::class, $results);
        $mixed = $results[\SchenkeIo\LaravelRelationManager\Tests\Models\MixedModel::class];

        $this->assertArrayNotHasKey('methodWithParam', $mixed);
        $this->assertArrayNotHasKey('methodWithInvalidAttribute', $mixed);
        $this->assertArrayNotHasKey('methodThatThrows', $mixed);
        $this->assertArrayNotHasKey('notARelation', $mixed);
        $this->assertArrayNotHasKey(\SchenkeIo\LaravelRelationManager\Tests\Models\NotAModel::class, $results);
    }

    public function test_get_database_columns_handles_exception()
    {
        $scanner = new ModelScanner;
        $this->assertEquals([], $scanner->getDatabaseColumns(['InvalidClass' => []]));
    }
}
