<?php

namespace SchenkeIo\LaravelRelationManager\Writer;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Nette;
use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Define\ProjectContainer;
use SchenkeIo\LaravelRelationManager\Define\RelationsEnum;
use SchenkeIo\LaravelRelationManager\Phpunit\AssertModelRelations;

class GenerateProjectTestFile
{
    public function __construct(
        protected Filesystem $filesystem = new Filesystem()
    ) {
    }

    public static function testGroup(): string
    {
        return class_basename(__CLASS__);
    }

    public function writeFile(Command $callingCommand, bool $testStrict): ?string
    {
        $relations = ProjectContainer::getRelations();
        $testProjectClass = config(ProjectContainer::CONFIG_KEY_PROJECT_TEST_CLASS);
        $extendedTestClass = config(ProjectContainer::CONFIG_KEY_EXTENDED_TEST_CLASS);
        $signature = $callingCommand->getName();
        $writerCallingClass = get_class($callingCommand);
        $rebuildCommand = (str_contains($signature, ':') ? 'php artisan ' : '').$signature;

        $file = new Nette\PhpGenerator\PhpFile;
        $file->addComment('## Test of all models defined');
        $file->addComment("\n------\n");
        $file->addComment('This file is auto-generated by: '.$writerCallingClass);
        $file->addComment('rewrite this test-file on the console with: '.$rebuildCommand);
        // https://laravel.com/docs/10.x/database-testing#resetting-the-database-after-each-test
        $migrationClass = 'Illuminate\Foundation\Testing\RefreshDatabase';
        $assertClass = AssertModelRelations::class;
        $nameSpace = $file->addNamespace(ClassData::take($testProjectClass)->nameSpace);
        $nameSpace->addUse($migrationClass);
        $nameSpace->addUse($assertClass);
        $nameSpace->addUse($writerCallingClass);
        $nameSpace->addUse($extendedTestClass);

        $class = $nameSpace->addClass(class_basename($testProjectClass));
        $class->setExtends($extendedTestClass);
        $class->addTrait($migrationClass);
        $class->addTrait($assertClass);
        /*
         * file age test
         */
        $method = $class->addMethod('testCommandFileIsOlderThanThisTestFile');
        $method->addComment('@return void');
        $method->addComment('@group '.self::testGroup());
        $method->addComment('');
        $method->addComment('Since this class is written by the Command file '.$writerCallingClass);
        $method->addComment('it is risky when changes in the Command file are not transferred here');
        $method->addComment('To update this file just run: '.$rebuildCommand);
        $method->setReturnType('void');
        $shortCommandClassName = class_basename($writerCallingClass).'::class';
        $method->addBody('$this->assertFirstClassIsOlderThanSecondClass(');
        $method->addBody("    $shortCommandClassName,");
        $method->addBody('    __CLASS__');
        $method->addBody(');');
        /*
         * loop over models
         */
        foreach ($relations as $baseModel => $relatedModels) {
            $relCount = count($relatedModels);
            $method = $class->addMethod(
                'testModel'.
                class_basename($baseModel).
                "Has_$relCount".
                ($testStrict ? 'Strict' : 'Tested').
                'Relationship'.
                ($relCount === 1 ? '' : 's').
                'AndWorks'
            );
            $method->addComment('Model '.$baseModel);
            $method->addComment('@group '.self::testGroup());
            $method->setReturnType('void');
            $method->addBody('$this->assertModelWorks("'.$baseModel.'");');
            if (is_array($relatedModels)) {
                $modelCount = 0;
                /**
                 * @var string $model2
                 * @var RelationsEnum $relation
                 */
                foreach ($relatedModels as $model2 => $relation) {
                    if ($relation == RelationsEnum::noRelation) {
                        continue;
                    }
                    $assertName = $relation->getAssertName();
                    $method->addBody("\$this->$assertName('$baseModel', '$model2');");
                    $modelCount++;
                }
                if ($testStrict) {
                    $method->addBody("\$this->assertModelRelationCount('$baseModel', $modelCount);");
                }
            } else {
                /*
                 * single
                 */
                $method->addBody("\$this->assertIsSingle('$baseModel');");
            }
        }
        $fileName = ClassData::take($testProjectClass)->fileName;
        try {

            $printer = new Nette\PhpGenerator\PsrPrinter;
            $this->filesystem->put($fileName, $printer->printFile($file));

            return null;
        } catch (Exception $e) {
            return $e->getMessage();
        }

    }
}
