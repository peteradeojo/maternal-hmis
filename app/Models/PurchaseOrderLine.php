<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderLine extends Model
{
    protected $fillable = [
        'po_id', 'item_id', 'qty_ordered',
        'unit', 'unit_cost', 'qty_received',
    ];
}
