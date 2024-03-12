<?php

namespace SchenkeIo\LaravelRelationManager\Define;

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
        RelationsEnum $forward,
        RelationsEnum $reverse
    ): self {
        ProjectContainer::addModel($modelName);
        ProjectContainer::addRelation($this->primaryModel, $modelName, $forward);
        if ($addReverseRelation) {
            ProjectContainer::addRelation($modelName, $this->primaryModel, $reverse);
        }
        if ($forward == RelationsEnum::castEnum) {
            ProjectContainer::addRelation($modelName, $this->primaryModel, RelationsEnum::castEnumReverse);
        }

        return $this;
    }
}
