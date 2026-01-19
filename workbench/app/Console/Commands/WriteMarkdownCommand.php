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

            $assembler->autoHeader()
                ->addMarkdown('header.md')
                ->addTableOfContents()
                ->addMarkdown('installation.md')
                ->addMarkdown('usage.md')
                ->addMarkdown('examples.md')
                ->addMarkdown('testing.md')
                ->writeMarkdown('README.md');

            $this->info('README.md generated successfully.');
        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());
        }
    }
}
