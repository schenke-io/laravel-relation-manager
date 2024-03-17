<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use BackedEnum;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Nette\PhpGenerator\PhpFile;
use ReflectionClass;
use SchenkeIo\LaravelRelationManager\Exceptions\LaravelNotLoadedException;
use Spatie\LaravelData\Data;

class ClassData extends Data
{
    public string $classError = '';

    public string $modelError = '';

    public readonly mixed $reflection;

    public readonly bool $isClass;

    public readonly bool $isModel;

    public readonly bool $isBackedEnum;

    public readonly bool $isModelOrBackedEnum;

    public readonly string $fileName;

    public readonly string $nameSpace;

    public readonly int $fileAge;

    public readonly string $className;

    public function __construct(protected string $class, protected Filesystem $fileSystem = new Filesystem)
    {
        try {
            $this->reflection = new ReflectionClass($class);
        } catch (\ReflectionException $e) {
            $this->reflection = null;
            $this->classError = $e->getMessage();
            $this->isClass = false;
            $this->isModel = false;
            $this->isBackedEnum = false;
            $this->isModelOrBackedEnum = false;
            $this->className = '';
            $this->nameSpace = '';
            $this->fileName = '';
            $this->fileAge = -1;

            return;
        }

        $this->isClass = true;
        $this->className = $this->reflection->getName();
        $this->fileName = $this->reflection->getFileName();
        $this->nameSpace = $this->reflection->getNamespaceName();
        $this->fileAge = $this->fileSystem->exists($this->fileName) ?
            $this->fileSystem->lastModified($this->fileName) : -1;

        if ($this->reflection->isSubclassOf(Model::class)) {
            $this->isModel = true;
            $this->isBackedEnum = false;
            $this->isModelOrBackedEnum = true;
        } elseif (self::isBackedEnum($class)) {
            $this->isModel = false;
            $this->isBackedEnum = true;
            $this->isModelOrBackedEnum = true;
        } else {
            $this->isModel = false;
            $this->isBackedEnum = false;
            $this->isModelOrBackedEnum = false;
            $this->modelError = "the class $class is no subclass of ".Model::class;
        }

    }

    public static function isBackedEnum($class): bool
    {
        if (! class_exists($class)) {
            return false;
        }

        $reflection = new ReflectionClass($class);

        // Check if the class implements the BackedEnum interface (PHP 8.1+)
        return interface_exists(BackedEnum::class) && $reflection->implementsInterface(BackedEnum::class);
    }

    public static function take(string $class): ClassData
    {
        return new ClassData($class);
    }

    public static function newFromName(string $nameSpace, string $className): ClassData
    {
        // check if name is ok already
        $classData = ClassData::take($className);
        if ($classData->isClass) {
            return $classData;
        }
        // assemble class name and try
        $classNameNew = Str::finish($nameSpace, '\\').$className;

        return ClassData::take($classNameNew);
    }

    public static function newFromFileName(string $fileName): ClassData
    {
        $file = PhpFile::fromCode(file_get_contents($fileName));
        $class = array_key_first($file->getClasses());

        return new ClassData($class);
    }

    /**
     * @throws LaravelNotLoadedException
     * @throws \ReflectionException
     */
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
     *
     * @throws LaravelNotLoadedException|\ReflectionException
     */
    public function getModelRelations(): array
    {
        if (! $this->isModelOrBackedEnum) {
            return [];
        }
        $return = [];

        try {
            DB::connection();
        } catch (Exception $e) {
            throw new LaravelNotLoadedException('Laravel is not loaded');
        }

        /*
         * find all from Eloquent
         */
        foreach ($this->reflection->getMethods() as $method) {
            if (str_starts_with(
                $method->getReturnType() ?? 'null return',
                'Illuminate\Database\Eloquent\Relations')) {
                // https://laracasts.com/discuss/channels/eloquent/is-there-a-way-to-list-all-relationships-of-a-model
                /**
                 * get the Model class in the method
                 *
                 * @var Model $model
                 */
                $model = new ($this->class);
                $modelMethod = $model->{$method->name}();
                $related = $modelMethod->getRelated();
                $theOtherModel = ($related)::class;
                $return[$theOtherModel] = class_basename($method->getReturnType()->getName());
            }
        }
        /*
         * find enum casts
         */
        if ($this->isModel) {
            $property = $this->reflection->getProperty('casts');
            $casts = $property->getValue(new $this->class);
            foreach ($casts as $name => $castClass) {
                if (self::isBackedEnum($castClass)) {
                    $return[$castClass] = 'BackedEnum';
                }
            }
        }

        return $return;
    }

    /**
     * returns text 'failed asserting that ....'
     *
     * @throws LaravelNotLoadedException
     * @throws \ReflectionException
     */
    public static function getRelationExpectation(
        string $class,
        string $returnType,
        string $usesClass): string
    {
        $relations = ClassData::take($class)->getModelRelations();
        if (isset($relations[$usesClass])) {
            if ($relations[$usesClass] == $returnType) {
                // expectation met, return OK
                return '';
            } else {
                return "$class has relation $returnType to $usesClass but found ".$relations[$usesClass];
            }
        } else {
            return "$class has a relation to $usesClass";
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
            ($this->fileAge >= $other->fileAge)
        ) {
            return true;
        }

        return false;
    }

    public function getShortName(): string
    {
        return $this->isClass ? $this->reflection->getShortName() : '';
    }

    public function getFileBase(): string
    {
        return basename($this->fileName);
    }
}
