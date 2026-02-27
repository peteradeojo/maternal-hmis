<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    const RECEIPT = "RECEIPT";
    const ISSUE = 'ISSUE';
    const TRANSFER = 'TRANSFER';
    const ADJUSTMENT = 'ADJUSTMENT';
    const RETURN = 'RETURN';
    const DISPOSAL = 'DISPOSAL';

    protected $fillable = [
        'tx_type',
        'item_id',
        'lot_id',
        'quantity',
        'unit',
        'unit_cost',
        'from_location_id',
        'to_location_id',
        'related_document',
        'reason',
        'performed_by',
    ];

    public function item()
    {
        return $this->belongsTo(StockItem::class, 'item_id');
    }

    public function from()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function to()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }
}
