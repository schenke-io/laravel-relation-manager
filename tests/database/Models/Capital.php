<?php

namespace SchenkeIo\LaravelRelationManager\Tests\database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Capital extends Model
{
    public $timestamps = false;

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
