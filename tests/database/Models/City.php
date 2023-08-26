<?php

namespace SchenkeIo\LaravelRelationManager\Tests\database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
