<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryBalance extends Model
{
    protected $fillable = [
        'item_id', 'location_id', 'lot_id',
        'qty_on_hand', 'last_updated',
    ];

    protected $with = [];

    protected $casts = [
        'qty_on_hand' => 'integer',
    ];

    public function item() {
        return $this->belongsTo(StockItem::class, 'item_id');
    }

    public function prices() {
        return $this->hasMany(StockItemPrice::class, 'item_id', 'item_id');
    }

    public function location() {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
