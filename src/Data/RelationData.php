<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use SchenkeIo\LaravelRelationManager\Define\RelationshipEnum;
use Spatie\LaravelData\Data;

class RelationData extends Data
{
    public function __construct(
        public string $model1,
        public string $model2,
        public RelationshipEnum $relationship
    ) {
    }

    public function getMermaidLine(): string
    {
        $classData1 = ClassData::take($this->model1);
        if (! $classData1->isModel) {
            return $this->model1.' is not a valid model';
        }
        $classData2 = ClassData::take($this->model2);
        if (! $classData2->isModel) {
            return $this->model2.' is not a valid model';
        }
        $name1 = ClassData::take($this->model1)->reflection->getShortName();
        $name2 = ClassData::take($this->model2)->reflection->getShortName();

        return $this->relationship->getMermaidLine($name1, $name2);
    }
}
