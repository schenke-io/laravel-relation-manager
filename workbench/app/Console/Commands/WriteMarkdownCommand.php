<?php

namespace Workbench\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use SchenkeIo\LaravelRelationManager\Enums\ConfigKey;
use SchenkeIo\PackagingTools\Badges\BadgeStyle;
use SchenkeIo\PackagingTools\Badges\MakeBadge;
use SchenkeIo\PackagingTools\Markdown\MarkdownAssembler;

class WriteMarkdownCommand extends Command
{
    protected $signature = 'write:markdown';

    protected $description = 'write trait files';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $mda = new MarkdownAssembler('workbench/resources/md');
        $mda->addMarkdown('header.md');
        $mda->addTableOfContents();
        $mda->addMarkdown('installation.md');

        $mda->addMarkdown('configuration.md');

        $table = [explode(',', 'key,definition,type')];
        foreach (ConfigKey::cases() as $case) {
            $table[] = [
                $case->value,
                $case->definition(),
                $case->type()->name,
            ];
        }
        $mda->addTableFromArray($table);

        $mda->addMarkdown('usage.md');
        $mda->addMarkdown('footer.md');
        $mda->writeMarkdown('README.md');
        $this->info('Markdown files written successfully.');

        MakeBadge::makeCoverageBadge('build/coverage/clover.xml', '32CD32')
            ->store('.github/coverage.svg', BadgeStyle::Flat);
        MakeBadge::makePhpStanBadge('phpstan.neon')
            ->store('.github/phpstan.svg', BadgeStyle::Flat);

    }
}
