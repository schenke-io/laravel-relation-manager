<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Highway extends Model
{
    public $timestamps = false;

    /**
     * @return BelongsToMany<City, $this>
     */
    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class);
    }

    /**
     * @return MorphMany<Location, $this>
     */
    public function locations(): MorphMany
    {
        return $this->morphMany(Location::class, 'locationable');
    }
}
