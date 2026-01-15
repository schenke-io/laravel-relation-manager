<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class City extends Model
{
    public $timestamps = false;

    /**
     * @return MorphToMany<Tag, $this>
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * @return BelongsTo<Region, $this>
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * @return BelongsToMany<Highway, $this>
     */
    public function highways(): BelongsToMany
    {
        return $this->belongsToMany(Highway::class);
    }

    /**
     * @return HasOneThrough<Country, Region, $this>
     */
    public function country(): HasOneThrough
    {
        return $this->hasOneThrough(Country::class, Region::class);
    }

    /**
     * @return MorphOne<Location, $this>
     */
    public function location(): MorphOne
    {
        return $this->morphOne(Location::class, 'locationable');
    }
}
