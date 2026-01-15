<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use SchenkeIo\LaravelRelationManager\Attributes\Relation;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation as RelationEnum;

class ReverseModel extends Model
{
    #[Relation(type: RelationEnum::hasMany, related: User::class, addReverse: true)]
    public function users() {}
}
