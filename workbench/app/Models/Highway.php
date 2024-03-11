<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Highway extends Model
{
    public $timestamps = false;

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class);
    }

    public function locations(): MorphMany
    {
        return $this->morphMany(Location::class, 'locationable');
    }
}
