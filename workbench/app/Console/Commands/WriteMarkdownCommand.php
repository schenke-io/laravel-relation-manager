<?php

namespace Workbench\App\Console\Commands;

use Illuminate\Console\Command;
use SchenkeIo\LaravelRelationManager\Data\RelationshipData;
use SchenkeIo\LaravelRelationManager\Support\PathResolver;
use SchenkeIo\LaravelRelationManager\Writer\GenerateMarkdownFile;
use SchenkeIo\PackagingTools\Markdown\MarkdownAssembler;

class WriteMarkdownCommand extends Command
{
    protected $signature = 'write:markdown';

    protected $description = 'Assembles the README.md from markdown fragments';

    public function handle(): void
    {
        $this->info('Assembling README.md...');

        try {
            $assembler = new MarkdownAssembler('workbench/resources/md');

            $assembler->addText('# Laravel Relation Manager');

            $assembler->storeVersionBadge();
            $assembler->storeTestBadge('run-tests.yml');
            $assembler->storeDownloadBadge();
            $assembler->storeLocalBadge('Coverage', '.github/coverage.svg');
            $assembler->storeLocalBadge('PHPStan', '.github/phpstan.svg');
            $assembler->addBadges();
            $assembler->addMarkdown('header.md');
            $assembler->addTableOfContents();

            $assembler->addMarkdown('installation.md');
            $assembler->addMarkdown('usage.md');

            /*
             * Inject Relation Visualization
             */
            try {
                $path = PathResolver::getRelationshipFilePath();
                $relationshipData = RelationshipData::loadFromFile($path);
                if ($relationshipData) {
                    $writer = new GenerateMarkdownFile($relationshipData);
                    $writer->generate('docs/relationships.md');
                    $assembler->addText("\n\n[View Model Relationships](docs/relationships.md)\n\n");
                }
            } catch (\Throwable $e) {
                $this->warn('Could not inject relation visualization: '.$e->getMessage());
            }

            $assembler->addMarkdown('examples.md');
            $assembler->addMarkdown('testing.md');

            $assembler->addText('---');
            $assembler->addText('README generated at '.date('Y-m-d H:i:s').' using [packaging-tools](https://github.com/schenke-io/packaging-tools)');

            $assembler->writeMarkdown('README.md');

            $this->info('README.md generated successfully.');
        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());
        }
    }
}
