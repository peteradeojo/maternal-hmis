<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    protected $fillable = [
        'requested_by', 'from_location_id', 'to_location_id',
        'status'
    ];

    protected $casts = [
        'status' => Status::class,
    ];
}
