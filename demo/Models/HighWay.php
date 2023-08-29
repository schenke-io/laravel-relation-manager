<?php

namespace SchenkeIo\LaravelRelationManager\Demo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HighWay extends Model
{
    public $timestamps = false;

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class);
    }
}
