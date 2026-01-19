<?php

namespace SchenkeIo\LaravelRelationManager\Scanner;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use SchenkeIo\LaravelRelationManager\Attributes\Relation as RelationAttribute;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;

/**
 * Service to analyze a single Eloquent model for relationships.
 *
 * This analyzer uses PHP Reflection to inspect a model class's public methods.
 * It identifies relationship definitions by looking for specialized #[Relation]
 * attributes or by analyzing method return types that match Eloquent relationship
 * classes. For each discovered relationship, it optionally instantiates the
 * model to extract runtime details such as the related model class name,
 * pivot table names, and foreign keys.
 */
class ModelAnalyzer
{
    protected ?Model $modelInstance = null;

    /**
     * @param  class-string<Model>  $className
     */
    public function __construct(protected string $className) {}

    /**
     * Analyzes the model class to discover its relationships.
     *
     * @param  array<string, array<string, array{type: EloquentRelation, related: string|null, pivotTable?: string, foreignKey?: string}>>  $models  Reference to the overall models array to allow injecting reverse relations.
     * @return array<string, array{type: EloquentRelation, related: string|null, pivotTable?: string, foreignKey?: string}>
     */
    public function analyze(array &$models): array
    {
        $reflection = new ReflectionClass($this->className);
        /** @var array<string, array{type: EloquentRelation, related: string|null, pivotTable?: string, foreignKey?: string}|false> $relations */
        $relations = [];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->getDeclaringClass()->getName() !== $this->className) {
                continue;
            }
            if ($method->getNumberOfParameters() > 0) {
                continue;
            }

            $methodName = $method->getName();
            $relation = $this->scanMethod($method, $models);

            if ($relation === false) {
                $relations[$methodName] = false;
            } elseif ($relation !== null) {
                $relations[$methodName] = $this->enrichRelationData($methodName, $relation);
            }
        }

        return array_filter($relations);
    }

    /**
     * Scans a single method for relationship definitions via attributes or return types.
     *
     * @param  array<string, array<string, array{type: EloquentRelation, related: string|null, pivotTable?: string, foreignKey?: string}>>  $models
     * @return array{type: EloquentRelation, related: string|null}|false|null
     */
    protected function scanMethod(ReflectionMethod $method, array &$models): array|false|null
    {
        // Check for #[Relation] attribute
        $attributes = $method->getAttributes(RelationAttribute::class);
        if (! empty($attributes)) {
            /** @var RelationAttribute $attrInstance */
            $attrInstance = $attributes[0]->newInstance();
            if ($attrInstance->type === EloquentRelation::noRelation) {
                return false;
            }

            if ($attrInstance->addReverse && $attrInstance->related) {
                $this->addReverseRelation($models, $attrInstance);
            }

            return [
                'type' => $attrInstance->type,
                'related' => $attrInstance->related,
            ];
        }

        // Check return type
        $returnType = $method->getReturnType();
        if ($returnType instanceof ReflectionNamedType) {
            $relationEnum = EloquentRelation::fromRelationName(class_basename($returnType->getName()));
            if ($relationEnum !== EloquentRelation::noRelation) {
                return [
                    'type' => $relationEnum,
                    'related' => null,
                ];
            }
        }

        return null;
    }

    /**
     * Injects an inverse relationship into the related model.
     *
     * @param  array<string, array<string, array{type: EloquentRelation, related: string|null, pivotTable?: string, foreignKey?: string}>>  $models
     */
    protected function addReverseRelation(array &$models, RelationAttribute $attrInstance): void
    {
        $inverseType = $attrInstance->type->inverse();
        if ($inverseType !== EloquentRelation::noRelation && $attrInstance->related) {
            $inverseMethod = strtolower(class_basename($this->className));
            if (! isset($models[$attrInstance->related])) {
                $models[$attrInstance->related] = [];
            }
            $models[$attrInstance->related][$inverseMethod] = [
                'type' => $inverseType,
                'related' => $this->className,
            ];
        }
    }

    /**
     * Attempts to get additional details by calling the relationship method.
     *
     * @param  array{type: EloquentRelation, related: string|null}  $relation
     * @return array{type: EloquentRelation, related: string|null, pivotTable?: string, foreignKey?: string}
     */
    protected function enrichRelationData(string $methodName, array $relation): array
    {
        try {
            $this->modelInstance = $this->modelInstance ?: new $this->className;
            /** @var object $relationObject */
            $relationObject = $this->modelInstance->$methodName();

            if (method_exists($relationObject, 'getRelated')) {
                $related = $relationObject->getRelated();
                if ($related instanceof Model) {
                    $relation['related'] = get_class($related);
                }
            }
            if ($relation['type'] === EloquentRelation::belongsToMany && method_exists($relationObject, 'getTable')) {
                $relation['pivotTable'] = (string) $relationObject->getTable();
            }
            if (method_exists($relationObject, 'getForeignKeyName')) {
                $relation['foreignKey'] = (string) $relationObject->getForeignKeyName();
            }
        } catch (\Throwable $e) {
            // Silently fail if method call fails (e.g. database not available or model incomplete)
        }

        return $relation;
    }
}
