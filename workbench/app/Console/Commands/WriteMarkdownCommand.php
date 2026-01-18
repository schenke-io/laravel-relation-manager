<?php

namespace Workbench\App\Console\Commands;

use Illuminate\Console\Command;
use SchenkeIo\PackagingTools\Badges\MakeBadge;
use SchenkeIo\PackagingTools\Markdown\MarkdownAssembler;

class WriteMarkdownCommand extends Command
{
    protected $signature = 'write:markdown';

    protected $description = 'Assembles the README.md from markdown fragments';

    public function handle(): void
    {
        MakeBadge::auto();
        $this->info('Assembling README.md...');

        try {
            $assembler = new MarkdownAssembler('workbench/resources/md');

            $assembler->addText('# Laravel Relation Manager');

            $assembler->badges()
                ->version()
                ->test('run-tests.yml')
                ->download();

            $assembler->addMarkdown('header.md')
                ->addTableOfContents()
                ->addMarkdown('installation.md')
                ->addMarkdown('usage.md')
                ->addMarkdown('examples.md')
                ->addMarkdown('testing.md')
                ->addText('---')
                ->addText('README generated at '.date('Y-m-d H:i:s').' using [packaging-tools](https://github.com/schenke-io/packaging-tools)')
                ->writeMarkdown('README.md');

            $this->info('README.md generated successfully.');
        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());
        }
    }
}
