<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use Illuminate\Console\Command;
use SchenkeIo\LaravelRelationManager\Define\RelationshipEnum;
use Spatie\LaravelData\Data;

class ProjectData extends Data
{
    /**
     * @var array<string, mixed>
     */
    public array $relation = [];

    /**
     * @var array<int, mixed>
     */
    public array $error = [];

    public readonly string $commandClassName;

    public readonly string $signature;

    /**
     * @param  ModelRelationData[]  $modelRelations
     */
    public function __construct(
        public readonly array $modelRelations = [],
        public readonly Command $command = new Command(),
        public readonly string $modelNamespace = 'App\Models',
        public readonly bool $strict = false
    ) {
        $this->commandClassName = get_class($this->command);
        $this->signature = $this->command->getName() ?? 'no signature';
        foreach ($this->modelRelations as $modelRelation) {
            $this->store($modelRelation);
        }
    }

    protected function store(ModelRelationData $modelRelation): void
    {
        $model1 = $this->fullModel($modelRelation->model1);
        if (is_null($modelRelation->model2)) {
            $this->setModel($model1);
        } else {
            $model2 = $this->fullModel($modelRelation->model2);
            $this->setModel($model2);
            $this->addError($this->getSetError($model1, $model2, $modelRelation->relation));
            $this->addError(
                $this->getSetError(
                    $model2,
                    $model1,
                    $modelRelation
                        ->relation
                        ->getInverse($modelRelation->noInverse)
                )
            );
        }
    }

    protected function getSetError(string $key1, string $key2, RelationshipEnum $relationship): ?string
    {
        if ($relationship == RelationshipEnum::isSingle) {
            $currentValue = $this->relation[$key1] ?? null;
            if (is_null($currentValue)) {
                $this->relation[$key1] = null;

                return null;
            } else {
                return "model '$key1' has relationships but is declared single";
            }
        }
        $currentValue = $this->relation[$key1][$key2] ?? null;
        if (is_null($currentValue)) {
            $this->relation[$key1][$key2] = $relationship;

            return null;
        } elseif ($currentValue === $relationship) {
            return null;
        } else {
            //dump($relationship->name,$relationship->getClass());
            return sprintf(
                '%s > %s: try to overwrite %s with %s',
                $key1, $key2,
                $this->relation[$key1][$key2]->name ?? null,
                $relationship->name
            );
        }
    }

    protected function setModel(string $model): void
    {
        $classScan = ClassData::take($model);
        if (! $classScan->isClass) {
            $this->error[] = "model name $model not found";
        } elseif (ClassData::take($model)->isModel) {
            $this->relation[$model] = $this->relation[$model] ?? [];
        } else {
            $this->error[] = "model name $model is invalid";
        }
    }

    /**
     * @return mixed[]
     */
    public function getAllModels(): array
    {
        return $this->relation;
    }

    public function getShortModelName(string $longModel): string
    {
        $parts = explode('\\', $longModel);

        return end($parts);
    }

    protected function addError(?string $message): void
    {
        if (! is_null($message)) {
            $this->error[] = $message;
        }
    }

    /**
     * @return mixed[]
     */
    public function getErrors(): array
    {
        return $this->error;
    }

    protected function fullModel(string $modelName): string
    {
        if (str_contains($modelName, '\\')) {
            return $modelName;
        } else {
            return $this->modelNamespace.'\\'.$modelName;
        }
    }

    /**
     * @param  string[]  $directories
     */
    public function scanDirectoriesForModels(array $directories): void
    {
        foreach ($directories as $directory) {
            foreach (glob("$directory\*.php") as $fileName) {
                $class = ClassData::newFromFileName($fileName);
                if ($class->isModel) {
                    $this->setModel($class->className);
                } else {
                    $this->error[] = sprintf('in directory %s PHP file found which is not a laravel Model: %s',
                        $directory,
                        $class->getFileBase()
                    );
                }
            }
        }
    }
}
