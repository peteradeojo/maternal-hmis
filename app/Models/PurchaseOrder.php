<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'supplier_id', 'po_number', 'status'
    ];

    protected $casts = [
        'status' => Status::class,
    ];
}
