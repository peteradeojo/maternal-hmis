<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillDetail extends Model
{
    protected $fillable = [
        'bill_id', 'user_id', 'description', 'quantity', 'unit_price', 'total_price',
        'chargeable_type', 'chargeable_id', 'tag', 'meta'
    ];

    public function bill() {
        return $this->belongsTo(Bill::class, 'bill_id');
    }
}
