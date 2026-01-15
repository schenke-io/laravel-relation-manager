<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SchenkeIo\LaravelRelationManager\Attributes\Relation;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation as RelationEnum;

class Post extends Model
{
    #[Relation(RelationEnum::belongsTo, related: User::class)]
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
