<?php

namespace SchenkeIo\LaravelRelationManager\Console;

use Illuminate\Console\Command;
use SchenkeIo\LaravelRelationManager\Data\RelationshipData;
use SchenkeIo\LaravelRelationManager\Enums\DiagramDirection;
use SchenkeIo\LaravelRelationManager\Support\PathResolver;
use SchenkeIo\LaravelRelationManager\Writer\GenerateMarkdownFile;

/**
 * Command to generate a markdown file containing a Mermaid diagram
 * based on the current relationship data.
 */
class RelationDrawCommand extends Command
{
    protected $signature = 'relation:draw {filename?}';

    protected $description = 'Generates markdown/diagrams from .relationships.json';

    public function handle(): int
    {
        $path = PathResolver::getRelationshipFilePath();

        $relationshipData = RelationshipData::loadFromFile($path);
        if (! $relationshipData) {
            $this->error('.relationships.json not found. Run relation:extract first.');

            return self::FAILURE;
        }

        $filename = $this->argument('filename');
        $filename = is_string($filename) ? $filename : $relationshipData->config->markdownPath;

        $writer = new GenerateMarkdownFile($relationshipData);
        if ($writer->generate($filename, DiagramDirection::LR)) {
            $this->info("Markdown written to $filename");
            foreach ($writer->getErrors() as $error) {
                $this->warn($error);
            }

            return self::SUCCESS;
        }

        $this->error("Failed to write to $filename");

        return self::FAILURE;
    }
}
