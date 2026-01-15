<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use SchenkeIo\LaravelRelationManager\Attributes\Relation;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation as RelationEnum;

class MixedModel extends Model
{
    public function methodWithParam($param)
    {
        return $this->hasMany(Post::class);
    }

    #[Relation(RelationEnum::noRelation)]
    public function methodWithInvalidAttribute()
    {
        return $this->hasMany(Post::class);
    }

    public function methodThatThrows()
    {
        throw new \Exception('error');
    }

    public function notARelation()
    {
        return 'string';
    }
}

class NotAModel {}
