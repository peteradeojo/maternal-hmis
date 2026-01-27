<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionLine extends Model
{
    protected $fillable = [
        'requisition_id', 'item_id', 'qty',
    ];
}
