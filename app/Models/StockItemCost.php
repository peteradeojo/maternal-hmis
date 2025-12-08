<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockItemCost extends Model
{
    const SOURCES = [
        'MANUAL' => 'MANUAL',
        'GRN' => 'GRN',
        'AUTO_ADJUST' => 'AUTO_ADJUST',
        'TRANSFER' => 'TRANSFER',
    ];

    protected $fillable = [
        'item_id', 'cost', 'source', 'lot_id'
    ];
}
