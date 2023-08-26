<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use Illuminate\Database\Eloquent\Model;
use Nette\PhpGenerator\PhpFile;
use ReflectionClass;
use Spatie\LaravelData\Data;

class ClassData extends Data
{
    public string $classError = '';

    public string $modelError = '';

    public readonly mixed $reflection;

    public readonly bool $isClass;

    public readonly bool $isModel;

    public readonly string $fileName;

    public readonly int $fileAge;

    public readonly string $className;

    public function __construct(protected string $class)
    {
        try {
            $this->reflection = new ReflectionClass($class);
        } catch (\ReflectionException $e) {
            $this->reflection = null;
            $this->classError = $e->getMessage();
            $this->isClass = false;
            $this->isModel = false;
            $this->className = '';
            $this->fileName = '';
            $this->fileAge = -1;

            return;
        }
        $this->isClass = true;
        $this->className = $this->reflection->getName();
        $this->fileName = $this->reflection->getFileName();
        $this->fileAge = file_exists($this->fileName) ? filemtime($this->fileName) : -1;

        if (! $this->reflection->isSubclassOf(Model::class)) {
            $this->isModel = false;
            $this->modelError = "the class $class is no subclass of ".Model::class;
        } else {
            $this->isModel = true;
        }
    }

    public static function take(string $class): ClassData
    {
        return new ClassData($class);
    }

    public static function newFromFileName(string $fileName): ClassData
    {
        $file = PhpFile::fromCode(file_get_contents($fileName));
        $class = array_key_first($file->getClasses());

        return new ClassData($class);
    }

    public static function getRelationCountOfModel(string $model): int
    {
        $classData = new ClassData($model);
        if ($classData->isModel) {
            return count($classData->getModelRelations());
        } else {
            return -1;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getModelRelations(): array
    {
        if (! $this->isModel) {
            return [];
        }
        $return = [];
        foreach ($this->reflection->getMethods() as $method) {
            if (str_starts_with(
                $method->getReturnType() ?? 'null return',
                'Illuminate\Database\Eloquent\Relations')) {
                // https://laracasts.com/discuss/channels/eloquent/is-there-a-way-to-list-all-relationships-of-a-model
                /*
                 * get the Model class in the method
                 */
                $model = new ($this->class);
                $modelMethod = $model->{$method->name}();
                $related = $modelMethod->getRelated();
                $theOtherModel = ($related)::class;
                $theOtherModel = ((new ($this->class))->{$method->name}()->getRelated())::class;
                $return[$theOtherModel] = $method->getReturnType();
            }
        }

        return $return;
    }

    public static function getRelationNotFoundError(
        string $class,
        string $returnType,
        string $usesClass): string
    {
        $relations = ClassData::take($class)->getModelRelations();
        if (isset($relations[$usesClass])) {
            if ($relations[$usesClass] == $returnType) {
                return '';
            } else {
                return "expected link from $class to $usesClass was $returnType but found ".$relations[$usesClass];
            }
        } else {
            return "no relation from $class to $usesClass found";
        }
    }

    /**
     * helper function
     */
    public function getFileAge(): int
    {
        if ($this->isClass) {
            return filemtime($this->reflection->getFileName());
        } else {
            return -1;
        }

    }

    /**
     * both class/files must exist and $otherClass must be
     * not older than this class
     */
    public function isFresherOrEqualThan(string $otherClass): bool
    {
        $other = new ClassData($otherClass);
        if (
            $this->isClass &&
            $other->isClass &&
            ($this->fileAge <= $other->fileAge)
        ) {
            return true;
        }

        return false;
    }

    public function getShortNameWithClass(bool $withClass = true): string
    {
        if ($this->isClass) {
            return $this->reflection->getShortName().
                ($withClass ? '::class' : '');
        } else {
            return '';
        }
    }

    public function getFileBase(): string
    {
        return basename($this->fileName);
    }
}
