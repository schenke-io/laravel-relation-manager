<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Location extends Model
{
    public $timestamps = false;

    public function locationable(): MorphTo
    {
        return $this->morphTo();
    }
}
