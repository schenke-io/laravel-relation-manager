<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use SchenkeIo\LaravelRelationManager\Attributes\Relation;
use SchenkeIo\LaravelRelationManager\Enums\Relation as RelationEnum;

class AttributeModel extends Model
{
    #[Relation(type: RelationEnum::hasOne, related: 'SchenkeIo\LaravelRelationManager\Tests\Models\User')]
    public function customRelation()
    {
        // no return type needed when attribute is present
    }
}
