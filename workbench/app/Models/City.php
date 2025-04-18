<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class City extends Model
{
    public $timestamps = false;

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function highways(): BelongsToMany
    {
        return $this->belongsToMany(Highway::class);
    }

    public function country(): HasOneThrough
    {
        return $this->hasOneThrough(Country::class, Region::class);
    }

    public function location(): MorphOne
    {
        return $this->morphOne(Location::class, 'locationable');
    }
}
