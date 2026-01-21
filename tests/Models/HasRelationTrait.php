<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasRelationTrait
{
    public function traitPosts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
