<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class ThroughModel extends Model
{
    public function userCountry(): HasOneThrough
    {
        return $this->hasOneThrough(Country::class, User::class);
    }

    public function userPosts(): HasManyThrough
    {
        return $this->hasManyThrough(Post::class, User::class);
    }
}

class Country extends Model {}
