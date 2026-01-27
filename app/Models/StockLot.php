<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLot extends Model
{
    protected $fillable = [
        'item_id',
        'lot_number',
        'manufacture_date',
        'expiry_date',
        'quantity_received',
    ];

    public function item()
    {
        return $this->belongsTo(StockItem::class, 'item_id');
    }

    public static function generateLotNumber($prefix = "???")
    {
        return fake()->lexify("{$prefix}-????-????");
    }
}
