<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockItemPrice extends Model
{
    const RETAIL = "RETAIL";
    const WHOLESALE = "WHOLESALE";
    const NHIS = "NHIS";
    const PRIVATE = "PRIVATE";
    const INTERNAL = "INTERNAL";
    const WARD = "WARD";

    protected $fillable = [
        'item_id',
        'price_type',
        'price',
        'currency',
        'effective_at',
        'created_by',
        'active',
    ];

    public function scopeType($query, $type)
    {
        $query->where('price_type', $type);
    }

    public function scopeActive($query) {
        $query->where('active', true);
    }
}
