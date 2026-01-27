<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    const INBOUND = 0;
    const STORE = 1;
    const OUTBOUND = 1000;

    protected $fillable = [
        'code', 'name', 'type', 'parent_id'
    ];
}
