<?php

namespace SchenkeIo\LaravelRelationManager\Demo;

use SchenkeIo\LaravelRelationManager\Console\RelationshipBuilder;
use SchenkeIo\LaravelRelationManager\Define\PrimaryModel;
use SchenkeIo\LaravelRelationManager\Demo\Models\Capital;
use SchenkeIo\LaravelRelationManager\Demo\Models\City;
use SchenkeIo\LaravelRelationManager\Demo\Models\Country;
use SchenkeIo\LaravelRelationManager\Demo\Models\HighWay;
use SchenkeIo\LaravelRelationManager\Demo\Models\Region;
use SchenkeIo\LaravelRelationManager\Demo\Models\Single;
use SchenkeIo\LaravelRelationManager\Exceptions\DirectoryNotWritableException;
use SchenkeIo\LaravelRelationManager\Exceptions\InvalidClassException;
use SchenkeIo\LaravelRelationManager\Tests\Define\TestProjectTest;
use Symfony\Component\Console\Command\Command;

//use Illuminate\Console\Command;

/**
 * this command works without Laravel being loaded
 */
class DemoCommand extends Command
{
    use RelationshipBuilder;

    protected string $signature = 'composer write-file';

    public const SUCCESS = 1;

    public function __construct()
    {
        parent::__construct($this->signature);
    }

    /**
     * @throws DirectoryNotWritableException
     */
    public function handle(): void
    {
        $this->projectIncludes([
            // instead of PrimaryModel::model the global function sayEach() can be used in Laravel
            PrimaryModel::model(Country::class)->hasOne(Capital::class),
            PrimaryModel::model(Country::class)->hasMany(Region::class),
            PrimaryModel::model(Region::class)->hasMany(City::class),
            PrimaryModel::model(Region::class)->hasOneThrough(Capital::class),
            PrimaryModel::model(City::class)->isManyToMany(HighWay::class),
            PrimaryModel::model(Single::class)->isSingle(),

        ])
            ->writeTestFileClassPhpunit(TestProjectTest::class)
            ->writeMermaidMarkdown(__DIR__.'/Doc/relations.md');

    }

    /**
     * called by composer
     *
     * @throws DirectoryNotWritableException
     * @throws InvalidClassException
     */
    public static function composerWrite(): void
    {
        $demoCommand = new DemoCommand();
        $demoCommand->handle();
    }

    public static function info(string $msg): void
    {
        echo "$msg\n";
    }

    public static function warn(string $msg): void
    {
        echo "$msg\n";
    }

    public static function error(string $msg): void
    {
        echo "$msg\n";
    }

    public static function call(string $msg): void
    {
        echo "call $msg\n";
    }
}
