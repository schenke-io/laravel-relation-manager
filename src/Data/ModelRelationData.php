<?php

namespace SchenkeIo\LaravelRelationManager\Data;

use Illuminate\Support\Str;
use SchenkeIo\LaravelRelationManager\Define\RelationsEnum;
use Spatie\LaravelData\Data;

/**
 * used in Phpunit/Constraints
 */
class ModelRelationData extends Data
{
    public function __construct(
        public readonly string $model1,
        public readonly ?string $model2,
        public readonly RelationsEnum $relation = RelationsEnum::noRelation,
        public readonly bool $preventInverse = false,
        public ?string $key1 = null,
        public ?string $key2 = null,
    ) {
        if ($relation->askForRelatedModel()) {
            $this->key1 = $key1 ?? 'id';
            $this->key2 = $this->getDefaultForeignKey($model1);
        } else {
            $this->key1 = $this->getDefaultForeignKey($model2 ?? '');
            $this->key2 = $key2 ?? 'id';
        }
    }

    protected function getDefaultForeignKey(string $className): string
    {
        $baseName = basename(str_replace('\\', '/', $className));

        return Str::snake($baseName).'_id';
    }

    /*  public function mainRelation(): ModelRelationData
      {
          return new ModelRelationData(
              $this->model1,
              $this->model2 ?? '',
              $this->relation,
              $this->key1,
              $this->key2
          );
      }*/

    /* public function inverseRelation(): ModelRelationData
     {
         return new ModelRelationData(
             $this->model2 ?? '',
             $this->model1,
             $this->relation->inverse(),
             $this->key2,
             $this->key1
         );
     }*/

    /*  public function hasInverse(): bool
      {
          if (is_null($this->model2)) {
              return false;
          } else {
              return $this->relation->hasInverse($this->preventInverse);
          }
      }*/

    /*public function getMermaidLine(): string
    {
        $classData1 = ClassData::take($this->model1 ?? '');
        if (! $classData1->isModel) {
            return $this->model1.' is not a valid model';
        }
        $classData2 = ClassData::take($this->model2 ?? '');
        if (! $classData2->isModel) {
            return $this->model2.' is not a valid model';
        }
        $name1 = ClassData::take($this->model1)->reflection->getShortName();
        $name2 = ClassData::take($this->model2)->reflection->getShortName();

        return $this->relation->getMermaidLine($name1, $name2);
    }*/
}
