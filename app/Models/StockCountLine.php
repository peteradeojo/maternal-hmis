<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockCountLine extends Model
{
    protected $fillable = [
        'stock_count_id', 'item_id', 'lot_id',
        'counted_qty',
    ];
}
