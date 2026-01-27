<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockCountLine extends Model
{
    protected $fillable = [
        'stock_count_id', 'item_id', 'lot_id',
        'counted_qty', 'system_qty',
    ];

    public function stock_count() {
        return $this->belongsTo(StockCount::class, 'stock_count_id');
    }

    public function item() {
        return $this->belongsTo(StockItem::class, 'item_id');
    }
}
