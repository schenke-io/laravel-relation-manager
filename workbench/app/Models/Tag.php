<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    public $timestamps = false;

    /**
     * Get all the cities that are assigned this tag.
     *
     * @return MorphToMany<City, $this>
     */
    public function cities(): MorphToMany
    {
        return $this->morphedByMany(City::class, 'taggable');
    }

    /**
     * Get all the regions that are assigned this tag.
     *
     * @return MorphToMany<Region, $this>
     */
    public function regions(): MorphToMany
    {
        return $this->morphedByMany(Region::class, 'taggable');
    }
}
