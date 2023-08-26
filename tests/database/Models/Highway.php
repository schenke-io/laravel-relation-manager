<?php

namespace SchenkeIo\LaravelRelationManager\Tests\database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Highway extends Model
{
    public $timestamps = false;

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class);
    }
}
