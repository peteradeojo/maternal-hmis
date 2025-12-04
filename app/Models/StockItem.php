<?php

namespace App\Models;

use Database\Factories\StockItemFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class StockItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'category',
        'is_pharmaceutical',
        'requires_lot',
        'base_unit',
    ];

    const CATEGORIES = ['DRUG','LAB', 'CONSUMABLE'];

    public function balance()
    {
        return $this->hasOne(InventoryBalance::class, 'item_id');
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class, 'item_id');
    }

    public function lots()
    {
        return $this->hasMany(StockLot::class, 'item_id');
    }

    public function prices()
    {
        return $this->hasMany(StockItemPrice::class, 'item_id')->latest();
    }
}
