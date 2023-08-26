<?php

namespace SchenkeIo\LaravelRelationshipManager\Tests\database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Country extends Model
{
    public $timestamps = false;

    public function capital(): HasOne
    {
        return $this->hasOne(Capital::class);
    }

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    public function cities(): HasManyThrough
    {
        return $this->hasManyThrough(City::class, Region::class);
    }
}
