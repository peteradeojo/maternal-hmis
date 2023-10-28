<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait Visit {
    public function type(): Attribute {
        return Attribute::make(get: fn() => $this->getType());
    }
}
