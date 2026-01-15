<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Capital extends Model
{
    public $timestamps = false;

    /**
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return MorphOne<Location, $this>
     */
    public function location(): MorphOne
    {
        return $this->morphOne(Location::class, 'locationable');
    }
}
