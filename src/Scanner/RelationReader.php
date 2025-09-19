<?php

namespace SchenkeIo\LaravelRelationManager\Scanner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Filesystem\Filesystem;
use ReflectionClass;
use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Enums\ConfigKey;
use SchenkeIo\LaravelRelationManager\Enums\Relation;
use SchenkeIo\LaravelRelationManager\Exceptions\LaravelNotLoadedException;
use Throwable;

class RelationReader
{
    public const RELATION_CLASSES = [
        Relations\BelongsTo::class,
        Relations\BelongsToMany::class,
        Relations\HasMany::class,
        Relations\HasManyThrough::class,
        Relations\HasOne::class,
        Relations\HasOneThrough::class,
        Relations\MorphMany::class,
        Relations\MorphOne::class,
        Relations\MorphTo::class,
        Relations\MorphToMany::class,
    ];

    public function __construct(protected Filesystem $files = new Filesystem) {}

    /**
     * looping through all models and showing the code this structure would be defined
     *
     * @throws LaravelNotLoadedException
     */
    public function displayRelations(): string
    {
        $return = '';
        $relationData = $this->relationData();
        foreach ($relationData as $model => $relations) {
            $modelRelations = [];
            foreach ($relations as $otherModel => $relationNames) {
                foreach ($relationNames as $relationName) {
                    $relation = Relation::fromRelationName($relationName);
                    if ($relation === Relation::noRelation) {
                        continue;
                    }
                    if ($relation->hasPublicFunction()) {
                        // check for reverse
                        $hasInverse = false;
                        $otherRelations = $relationData[$otherModel][$model] ?? [];
                        foreach ($otherRelations as $otherRelation) {
                            if (lcfirst($otherRelation) == $relation->inverse()->name) {
                                $hasInverse = true;
                            }
                        }
                        if ($relation->hasInverse()) {
                            $modelRelations[] = sprintf('    ->%s(%s, %s)',
                                lcfirst($relationName),
                                $otherModel, $hasInverse ? 'true' : 'false'
                            );
                        } else {
                            $modelRelations[] = sprintf('    ->%s(%s)',
                                lcfirst($relationName),
                                $otherModel
                            );
                        }
                    }
                }
            }
            if (! empty($modelRelations)) {
                $return .= "\n\$this->relationManager->model($model)";
                foreach ($modelRelations as $relation) {
                    $return .= "\n$relation";
                }
                $return .= ";\n";
            }
        }

        return $return;
    }

    /**
     * @return array<class-string, array<class-string, list<string>>>
     *
     * @throws LaravelNotLoadedException
     */
    public function relationData(): array
    {
        $return = [];
        foreach ($this->getModelPaths() as $path) {
            $class = $this->getClassFromPath($path);
            if (! $class || ! $this->isInstantiableModel($class)) {
                continue;
            }
            $relations = (new ClassData($class))->getModelRelations();

            if (! empty($relations)) {
                $return[$class] = $relations;
            }
        }

        return $return;
    }

    /**
     * Get all PHP file paths in the app/Models directory.
     *
     * @return list<string>
     */
    protected function getModelPaths(): array
    {
        $directory = (string) ConfigKey::MODEL_DIRECTORY->get();
        if (! $this->files->isDirectory($directory)) {
            return [];
        }

        $files = $this->files->allFiles($directory);

        $paths = [];
        foreach ($files as $file) {
            $paths[] = $file->getPathname();
        }

        return $paths;
    }

    /**
     * Get the fully qualified class name from a file path.
     *
     * @return class-string|null
     */
    protected function getClassFromPath(string $path): ?string
    {
        // Replace the app path with the base namespace 'App'
        $class = ConfigKey::MODEL_NAME_SPACE->get().'\\'.basename($path, '.php');

        return class_exists($class) ? $class : null;
    }

    /**
     * Check if a class is an instantiable Eloquent model.
     *
     * @param  class-string  $class
     */
    protected function isInstantiableModel(string $class): bool
    {
        try {
            $reflection = new ReflectionClass($class);

            return $reflection->isSubclassOf(Model::class) && ! $reflection->isAbstract();
        } catch (Throwable $e) {
            return false;
        }
    }
}
