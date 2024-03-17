<?php

namespace SchenkeIo\LaravelRelationManager\Writer;

use Nette;
use SchenkeIo\LaravelRelationManager\Data\ModelCountData;
use SchenkeIo\LaravelRelationManager\Data\ModelRelationData;
use SchenkeIo\LaravelRelationManager\Define\RelationsEnum;

class GenerateAssertModelRelationsTrait
{
    public static function getContent(): string
    {
        $location = 'SchenkeIo\LaravelRelationManager';
        $traitName = 'AssertModelRelations';
        $file = new Nette\PhpGenerator\PhpFile;

        $file->addComment('## possible assertions of Eloquent Models');
        $file->addComment('add this file into your Phpunit test files');
        $file->addComment("\n------\n");
        $file->addComment('This file is auto-generated by: ');
        $file->addComment(__CLASS__);
        $file->addComment('using the data from: @see '.RelationsEnum::class);

        $nameSpace = $file->addNamespace($location.'\Phpunit');
        $nameSpace->addUse(ModelCountData::class);
        $nameSpace->addUse(ModelRelationData::class);
        $nameSpace->addUse(RelationsEnum::class);
        $classWritten = [];
        /*        foreach (RelationshipEnum::cases() as $case) {
                    if ($case->isRelation()) {
                        $class = $case->getClass();
                        if (! in_array($class, $classWritten)) {
                            $nameSpace->addUse($class);
                            $classWritten[] = $class;
                        }
                    }
                }*/

        $trait = $nameSpace->addTrait($traitName);

        /*
         * does the model exists and works ?
         */
        $method = $trait->addMethod('assertModelBackedEnumWorks');
        $method->addParameter('model')->setType('string');
        $method->setReturnType('void');
        $method->addBody('\PHPUnit\Framework\assertThat($model, new ModelBackedEnumConstraint());');
        /*
         * check if first class is older than second
         */
        $method = $trait->addMethod('assertFirstClassIsOlderThanSecondClass');
        $method->addParameter('class1')->setType('string');
        $method->addParameter('class2')->setType('string');
        $method->setReturnType('void');
        $method->addBody('\PHPUnit\Framework\assertThat(');
        $method->addBody('    new ModelRelationData($class1, $class2, RelationsEnum::noRelation),');
        $method->addBody('    new ClassAgeConstraint()');
        $method->addBody(');');
        /*
         * count the relations in a model
         */
        $method = $trait->addMethod('assertModelRelationCount');
        $method->addParameter('model')->setType('string');
        $method->addParameter('count')->setType('int');
        $method->setReturnType('void');
        $method->addBody('\PHPUnit\Framework\assertThat(');
        $method->addBody('    new ModelCountData($model, $count),');
        $method->addBody('    new RelationshipCountConstraint()');
        $method->addBody(');');

        foreach (RelationsEnum::cases() as $case) {
            if ($case == RelationsEnum::isSingle) {
                $methodName = 'assertModelIsSingle';
                $method = $trait->addMethod($methodName);
                $method->addParameter('model')->setType('string');
                $method->setReturnType('void');
                $method->addBody('\PHPUnit\Framework\assertThat($model, new NoRelationshipConstraint());');
            } elseif ($case == RelationsEnum::castEnumReverse) {
                // we flip the models and flip the enum
                $method = $trait->addMethod($case->getAssertName());
                $method->addParameter('modelFrom')->setType('string');
                $method->addParameter('modelTo')->setType('string');
                $method->setReturnType('void');
                $method->addBody('\PHPUnit\Framework\assertThat(');
                $method->addBody('    new ModelRelationData($modelTo, $modelFrom, RelationsEnum::castEnum),');
                $method->addBody('    new RelationshipExistsConstraint()');
                $method->addBody(');');
            } elseif ($case == RelationsEnum::morphTo) {
                $method = $trait->addMethod($case->getAssertName());
                $method->addParameter('modelFrom')->setType('string');
                $method->setReturnType('void');
                $method->addBody('\PHPUnit\Framework\assertThat(');
                $method->addBody('    new ModelRelationData($modelFrom, $modelFrom, RelationsEnum::'.$case->name.'),');
                $method->addBody('    new RelationshipExistsConstraint()');
                $method->addBody(');');
            } elseif ($case->isRelation()) {
                $method = $trait->addMethod($case->getAssertName());
                $method->addParameter('modelFrom')->setType('string');
                $method->addParameter('modelTo')->setType('string');
                $method->setReturnType('void');

                $method->addBody('\PHPUnit\Framework\assertThat(');
                $method->addBody('    new ModelRelationData($modelFrom, $modelTo, RelationsEnum::'.$case->name.'),');
                $method->addBody('    new RelationshipExistsConstraint()');
                $method->addBody(');');
            }

        }
        $printer = new Nette\PhpGenerator\PsrPrinter;

        return $printer->printFile($file);
    }
}
