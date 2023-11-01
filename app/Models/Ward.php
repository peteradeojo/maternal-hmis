<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** @property string $name */
/** @property string $type */
/** @property int $beds */
/** @property int $filled_beds */
class Ward extends Model
{
    use HasFactory;

    protected $guarded = null;

    protected $appends = ['available_beds'];

    public function availableBeds(): Attribute {
        return Attribute::make(get: fn () => $this->beds - $this->filled_beds);
    }
}
