<?php

namespace Workbench\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use SchenkeIo\LaravelRelationManager\Console\WriteTraitFiles;

class WriteMainFilesCommand extends Command
{
    protected $signature = 'write:main-files';

    protected $description = 'write trait files';

    public function handle(): void
    {
        (new WriteTraitFiles(new Filesystem))->generate();
        $this->info('2 trait files written');
    }
}
