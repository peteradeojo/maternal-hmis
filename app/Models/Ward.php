<?php

namespace App\Models;

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
}
