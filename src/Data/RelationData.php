<?php

namespace SchenkeIo\LaravelRelationshipManager\Data;

use Spatie\LaravelData\Data;

class RelationData extends Data
{
    /**
     * @param  string  $relationship class name from Illuminate\Database\Eloquent\Relations\*
     */
    public function __construct(
        public string $model1,
        public string $model2,
        public string $relationship = ''
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

        return "$name1 ---- $name2\n";
    }
}
