<?php

namespace App\Traits;

use App\Models\Vitals;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait Visit
{
    public function type(): Attribute
    {
        return Attribute::make(get: fn () => $this->getType());
    }

    public function vitals()
    {
        return $this->morphMany(Vitals::class, 'recordable');
    }
}
