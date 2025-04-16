<?php

namespace SchenkeIo\LaravelRelationManager\Define;

use SchenkeIo\LaravelRelationManager\Enums\Relation;

class DefineRelation
{
    use RelationTypes;

    private readonly string $primaryModel;

    public function __construct(string $modelFrom)
    {
        $this->primaryModel = $modelFrom;
        ProjectContainer::addModel($modelFrom);
    }

    private function buildRelation(
        string $modelName,
        bool $addReverseRelation,
        Relation $forward,
        Relation $reverse
    ): self {
        ProjectContainer::addModel($modelName);
        ProjectContainer::addRelation($this->primaryModel, $modelName, $forward);
        if ($addReverseRelation) {
            ProjectContainer::addRelation($modelName, $this->primaryModel, $reverse);
        }

        return $this;
    }
}
