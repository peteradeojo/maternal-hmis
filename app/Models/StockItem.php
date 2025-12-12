<?php

namespace App\Models;

use Database\Factories\StockItemFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockItem extends Model
{
    use HasFactory, SoftDeletes;

    const CATEGORIES = ['DRUG', 'LAB', 'CONSUMABLE'];

    const units = [
        'tab' => 'Tablet',
        'bottle' => 'Bottle',
        'satchet' => 'Satchet',
        'ampoule' => 'ampoule',
        'vial' => 'Vial',
        'bag' => 'Bag',
        'capsule' => 'Capsule',
        'unit' => 'Unit',
    ];

    protected $fillable = [
        'sku',
        'name',
        'description',
        'category',
        'is_pharmaceutical',
        'requires_lot',
        'base_unit',
        'weight',
        'si_unit',
    ];

    protected $appends = ['balance'];

    public function balances()
    {
        return $this->hasMany(InventoryBalance::class, 'item_id');
    }

    public function balance(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->balances->sum('qty_on_hand'),
        );
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class, 'item_id');
    }

    public function lots()
    {
        return $this->hasMany(StockLot::class, 'item_id');
    }

    public function prices($type = null)
    {
        $query = $this->hasMany(StockItemPrice::class, 'item_id')->where('active', true)->latest();

        if ($type) {
            $query = $query->where('price_type', $type);
        }

        return $query;
    }

    public function costs()
    {
        return $this->hasMany(StockItemCost::class, 'item_id');
    }
}
